<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\Persistence\ManagerRegistry;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'app:add-admin',
    description: 'Creates a new admin.',
    hidden: false,
    aliases: ['app:create-admin']
)]
class AddAdminCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:add-admin';
    private ManagerRegistry $doctrine;
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->doctrine = $doctrine;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output): int
    {
        $output->writeln([
            'Admin Creator',
            '=============',
            '',
            'You are about to create a user.',
            ''
        ]);

        $helper = $this->getHelper('question');

        // Ask for email
        $emailQuestion = new Question('Please enter a new email address: ');
        
        // Normalizer is used to trim the text (remove whitespaces on beginning and end).
        $emailQuestion->setNormalizer(function ($value) {
            // $value can be null here
            return $value ? trim($value) : '';
        });

        $emailQuestion->setValidator(function ($value) {
            return $this->validateEmail($value);
        });

        $email = $helper->ask($input, $output, $emailQuestion);

        if(!$email) {
            $output->writeln('Skipping creation');
            return Command::SUCCESS;
        }

        // Ask for password
        $passwordQuestion = new Question('Please enter a password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);

        $passwordQuestion->setValidator(function ($value) {
            return $this->validatePassword($value);
        });

        $password = $helper->ask($input, $output, $passwordQuestion);

        // Create user
        $admin = new Admin();
        $admin->setEmail($email);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, $password)
        );

        $em = $this->doctrine->getManager();
        $em->persist($admin);
        $em->flush();

        $output->writeln([
            'Admin created successfully.'
        ]);

        return Command::SUCCESS;
    }

    private function validateEmail($value) {
        // Check, that mail is not blank.
        $emailConstraint = new Assert\NotBlank();
        // $emailAssert->message = "some text";
        $errors = $this->validator->validate($value, $emailConstraint);

        if($errors->count() > 0) {
            throw new \RuntimeException('Email address cannot be empty.');
        }

        // Check, that mail is valid
        $emailConstraint = new Assert\Email();
        // $emailAssert->message = "some text";
        $errors = $this->validator->validate($value, $emailConstraint);

        if($errors->count() > 0) {
            throw new \RuntimeException('Email address is invalid.');
        }
        
        // Check that the mail is not already in use.
        if($this->newMailIsInUse($this->doctrine, $value)) {
            throw new \RuntimeException('This email is already in use.');
        }

        return $value;
    }

    private function validatePassword($value) {
        // Check, that mail is not blank.
        $passwordConstraint = new Assert\NotBlank();
        // $emailAssert->message = "some text";
        $errors = $this->validator->validate($value, $passwordConstraint);

        if($errors->count() > 0) {
            throw new \RuntimeException('Password cannot be empty.');
        }

        // Additional checks for the password.
        $passwordConstraint = new PasswordRequirements([
            'minLength' => 6,
            'requireLetters' => true,
            'requireCaseDiff' => true,
            'requireNumbers' => true,
            'requireSpecialCharacter' => true
        ]);
        $errors = $this->validator->validate($value, $passwordConstraint);

        if($errors->count() > 0) {
            throw new \RuntimeException('Password must have a minimum length of 6 characters and contain a lowercase letter, uppercase letter, number and special character.');
        }

        return $value;
    }


    /**
     * Checks, if an emailAddress is in use by a different account,
     * than the current one.
     *
     * @param  ManagerRegistry $doctrine
     * @param  User $user
     * @param  string $email
     * @return bool
     */
    private function newMailIsInUse(
        ManagerRegistry $doctrine,
        string $email): bool
    {
        // Try to load an admin with this email address from the database.
        $repository = $doctrine->getRepository(Admin::class);
        $emailUser = $repository->findOneBy(
            [
                'email' => $email
            ]
        );

        if(null === $emailUser) {
            return false;
        }

        return true;
    }
}