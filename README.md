DrupalCI-API
============

## Overview

Provides a front facing API for the DrupalCI project. This allows us to change
the specific CI implementations as needed, without changing the API.

## API

Currently the API can only do two things: Start a job, and check on its status.

#### Job
A Job is a CI task sent off to Jenkins or similar test runner. Drupal.org or
other process requests that jobs be started, and then the test runner (Jenkins)
runs the CI process.

The Job has the following properties:

- id: Assigned when the Job is created by the API implementation.
- created: Timestamp of creation.
- type: The test type to run. e.g. 'simpletest', 'phpunit'...
- repository: Repository to test against.
- branch: Branch of the repository to check out.
- patch: File name of patch to apply to the branch of the repository.
- status: String indicating build phase.
- result: Pass/fail.
- log: Console output of the build thus far.

`POST [/job]`

Starts a job running. 

4xx response if:
- An ID is sent.
- Properties 'repository' and 'branch' are not sent.

`GET [/job/{id}]`

Query for the record with the given job ID. 404 if the ID does not exist.

#### Proposed extensions

These extensions to the API could be present in a future version.

`PUT [/job/{id}/cancel]`

Stop the job.

`PUT [/job/{id}/restart]`

Restarts the job. Implies cancel. Creates new id.

#### Structure

```json
Input

{
	"title": "This is a test build",
	"repository": "git://git.drupal.org/project/drupal.git",
	"branch": "8.0.x",
	"commit": "12353245",
	"patch": "https://www.drupal.org/files/issues/2304461-86.patch",
	"tags": [
		"Drupal 8",
		"8.0.x",
	],
	"tests": [
		{
			"type": "simpletest",
			"php": [
				"5.4",
				"5.5",
				"5.6",
				"master"
			],
			"db": [
				"mysql",
				"postgres",
				"mongodb"
			]
		},{
			"type": "phpunit",
			"php": [
				"5.4",
				"5.5",
				"5.6",
				"master"
			],
			"db": [
				"mysql",
				"postgres",
				"mongodb"
			]
		},{
			"type": "codesniffer",
			"php": [
				"5.4"
			]
		}
	]
}

Return

{
	"builds": [
		{
			"id": "1",
			"application": "php5.4",
			"service": "mysql",
			"results": "https://results.drupalci.org/node/1",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/1"
		},{
			"id": "2",
			"application": "php5.5",
			"service": "mysql",
			"results": "https://results.drupalci.org/node/2",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/2"
		},{
			"id": "3",
			"application": "php5.6",
			"service": "mysql",
			"results": "https://results.drupalci.org/node/3",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/3"
		},{
			"id": "4",
			"application": "php7.x",
			"service": "mysql",
			"results": "https://results.drupalci.org/node/4",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/4"
		},{
			"id": "5",
			"application": "php5.4",
			"service": "postgres",
			"results": "https://results.drupalci.org/node/5",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/5"
		},{
			"id": "6",
			"application": "php5.5",
			"service": "postgres",
			"results": "https://results.drupalci.org/node/6",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/6"
		},{
			"id": "7",
			"application": "php5.6",
			"service": "postgres",
			"results": "https://results.drupalci.org/node/7",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/7"
		},{
			"id": "8",
			"application": "php7.x",
			"service": "postgres",
			"results": "https://results.drupalci.org/node/8",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/8"
		}{
			"id": "9",
			"application": "php5.4",
			"service": "mongodb",
			"results": "https://results.drupalci.org/node/9",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/9"
		},{
			"id": "10",
			"application": "php5.5",
			"service": "mongodb",
			"results": "https://results.drupalci.org/node/10",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/10"
		},{
			"id": "11",
			"application": "php5.6",
			"service": "mongodb",
			"results": "https://results.drupalci.org/node/11",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/11"
		},{
			"id": "12",
			"application": "php7.x",
			"service": "mongodb",
			"results": "https://results.drupalci.org/node/12",
			"endpoint": "https://api.drupalci.org/drupalci/api/1/job/status/12"
		}
	],
}

{
	"id": "1",
	"title": "This is a test build",
	"status": "new",
	"result": "100 Passed, 1000000 Failed",
	"repository": "git://git.drupal.org/project/drupal.git",
	"branch": "8.0.x",
	"commit": "12353245",
	"patch": "https://www.drupal.org/files/issues/2304461-86.patch",
		"tags": [
		"Drupal 8",
		"8.0.x",
	],
	"application": "php5.4",
	"services": "mysql"
}
```

## Phing

### Installation

We use Phing as a build tool for this project. Please install Phing via the following instructions:

https://github.com/phingofficial/phing#installation

### Usage

We have a single task for this project that runs a series of steps. These range from preparation steps to testing. To run this build run the following command:

```
$ phing
```

## Vagrant

**Still to be implemented properly. Added to this project in an early release form.**

Vagrant is very handy. If you do not run Docker natively the following VM will provide a method for debugging and building and executing of containers locally.

Install Vagrant (1.6.x):

http://www.vagrantup.com/downloads.html
Spin up a VM with Docker with the following command:

```
$ vagrant up
```

## Deployment

Capistrano is a great tool for deployment web applications.

### Install

Capistrano can be installed via bundler (http://bundler.io). Run the following command:

```
bundle install --path vendor/bundle
```

To deploy to the DEV run the following command:

```
$ bundle exec cap dev deploy
```

To deploy to the PROD run the following command:

```
$ bundle exec cap prod deploy
```

## Puppet

### Installation

Puppet and Librarian Puppet can be installed via bundler (http://bundler.io). Check out the following script:

```
puppet/provision.sh
```
