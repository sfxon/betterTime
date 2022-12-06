# Testing with Cypress

Required files for cypress can be located in ```src/cypress```.
The tests are in ````src/cypress/e2e```.

Run tests from command line in src folder with:

```npx cypress open```

## Test-Creation

I use (DeploySentinal)[https://www.deploysentinel.com/docs/recorder] to help writing cypress tests.
It does a live recording of the inputs I click, and creates a first draft of test-code.
Usually I have to rewrite the code, to make it fit better.

## Previous integration steps

Cypress has been installed with the following command:

```
# cd [project-root]
cd src
npx cypress open
```

I have chosen the E2E Testing Template, since now it is a simple web-application.