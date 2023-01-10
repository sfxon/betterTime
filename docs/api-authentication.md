# API Authentication

## Update 2

At the moment, I want to go as near as the standards as possible.
That's why I choose the API-Bundle. I'll implement it with an oauth server,
tokens, and later with an additional acl.

I'll follow this documentation for parts of the integration:
https://symfonycasts.com/screencast/api-platform/install

## Update

I did some research and found out, that shopware uses PHP OAuth 2.0 Server by League.

https://github.com/thephpleague/oauth2-server

I'll dive into it, and check, how and if it fits our needs here.
First I will try to find some documentation, if and how we can use ith with the API Bundle.
I still try to avoid too much overhead work, but think, the API is important at this point.

## API Bundles

I have to make a decision, which kind of API approach I like to follow for BetterTime.
These are the possible options:

1. Use Symfonys API bundle:

https://symfony.com/doc/current/the-fast-track/de/26-api.html#cors-konfigurieren
https://symfony.com/doc/current/security/access_token.html
https://www.cloudways.com/blog/symfony-api-token-authentication/
https://symfony.com/doc/6.0/security/custom_authenticator.html

Dieser Weg ist vermutlich der bessere, da alle Symfony Core Bundles vermutlich am besten getestet und unterstützt sind.

2. Use a third party API bundle

## Authentication

The API bundles are by default only data providers.
This also means, that they are decoupled from authentication and authorization.
I have to implement authentication and authorization on  my own.

I decided, to have a look into the shopware 6 core, since they provide multiple APIs,
which also provide authentication.
It seems, like Shopware is using a custom approach. I guess, we could derivate from that approach somehow.

https://shopware.stoplight.io/docs/store-api/aa7ea5e14dea6-registering-a-customer
https://shopware.stoplight.io/docs/store-api/b08a8858e0c5d-register-a-customer
https://stackoverflow.com/questions/72965510/how-can-i-find-the-sw-context-token-while-registering-using-js
https://shopware.stoplight.io/docs/store-api/6b4f9a2acb576-security

After previous research, I could also go with a mechanism like this:

* OAuth2
https://davegebler.com/post/php/build-oauth2-server-php-symfony
https://github.com/hwi/HWIOAuthBundle

* JWT

https://curity.io/resources/learn/jwt-best-practices/
https://github.com/markitosgv/JWTRefreshTokenBundle
https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.rst#getting-started

OAuth2 seems to be the valid approach. JWT is not recommendet for end-user authentication,
because it might have some security flaws. But JWT is often used on top of Oauth2,
to reduce the amount of required requests to the server.
JWT provides an encrypted token, that can hold data, that can be used on client and server side.
OAuth2 is only for authentication, and uses more requests on the server-side to do so.

The greatest issue with OAuth2 I have so far is, that we have to implement our own OAuth2 server,
or use a third party login provider (which is nice, but I don't want external providers at the moment).
I am not sure, how big the amount of work is at that part at the moment.


Resources:

Oauth:
https://gist.github.com/lologhi/7b6e475a2c03df48bcdd
https://dev-qa.com/962162/symfony-spa-authentication-what-the-best-practice-currently
https://stackoverflow.com/questions/71056012/secure-login-with-jwt-or-oauth