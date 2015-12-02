# Freshdesk API client

[![Build Status](https://travis-ci.org/silktide/freshdesk-api.svg?branch=master)](https://travis-ci.org/silktide/freshdesk-api)
[![Code Climate](https://codeclimate.com/github/silktide/freshdesk-api/badges/gpa.svg)](https://codeclimate.com/github/silktide/freshdesk-api)
[![Test Coverage](https://codeclimate.com/github/silktide/freshdesk-api/badges/coverage.svg)](https://codeclimate.com/github/silktide/freshdesk-api/coverage)

A PHP API client for the Freshdesk API.

## Installation

    composer require silktide/freshdesk-api

## Usage

This library was built with dependency injection (DI) in mind, but a factory is included for your convenience should you prefer. 

### With DI

The client class has 5 dependencies:

 1. Instance of `GuzzleHttp\Client`
 2. Instance of `Silktide\FreshdeskApi\ResponseFactory`
 3. Your Freshdesk domain (e.g. 'https://mydomain.freshdesk.com')
 4. Your API key or username
 5. Your password (optional, omit if using API key)

### Without DI

    $client = \Silktide\FreshdeskApi\ClientFactory::create('https://mydomain.freshdesk.com', 'myApiKeyOrUsername', 'password');
    
Password is optional and should be omitted if you're using an API key.

## Supported requests
 
### Submitting a ticket

    $client->submitTicket([
        'description' => 'A message',
        'subject' => 'A subject',
        'email' => 'email@domain.com",
        'priority '=> \Silktide\FreshdeskApi\Constant::PRIORITY_MEDIUM
        'status' => \Silktide\FreshdeskApi\Constant::STATUS_OPEN
    ]);