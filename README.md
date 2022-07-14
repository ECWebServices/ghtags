<a href="https://supportukrainenow.org/"><img src="https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner-direct.svg" width="100%"></a>

------

# GHTags

So, you're using something like [Envoyer](https://envoyer.io) and [sebastian/version](https://packagist.org/packages/sebastian/version) doesn't work?

Then you can use GHTags!

## Requirements

- PHP 8.0+
- PHP SQLite3 extension
- GitHub Personal Access Token (only "repo" scope is required)

## About

GHTags is a simple tool for getting your projects tags from GitHub. This includes private repositories (as long as you have enabled access to them).

GHTags was created because I wanted to use [sebastian/version](https://packagist.org/packages/sebastian/version) for my projects, but it didn't work, as Envoyer doesn't use the actual Git repository, and instead downloads the latest version as a tarball.
So I would be getting the "vfatal: not a git repository (or any of the parent directories): .git" error. Sure, there are some workarounds, like making a subdirectory of where you're actually cloning your repo to the actual repo, but I didn't want to deal with that, as I wanted to lower my storage usage.

## Installation

Installing is as simple as a composer install:

```shell
composer global require ecwebservices/ghtags
```

Once you've done that, you'll need to create a Personal Access Token in your GitHub account. If you set the expiration date to "Never", you'll be able to use it forever.

The token will need to have the "repo" scope. This allows you to get the tags from your repos.

Then we'll run the setup command:

```shell
ghtags setup
```

Once you've done that, you can head to Usage and see how to use GHTags.

If you ever want to change your token, you can run the set:key command.

```shell
ghtags set:key <token>
```

## Usage

### Initialization

To initialize GHTags, run the setup command from the root of your project:

```shell
ghtags repo:new
```
### Updating Tags

```shell
ghtags update
```

This will get all the tags from the repo and update the database with the tags.

### Syncing Tags

```shell
ghtags sync:all
```

This will get the latest tags from the repo and update your tag file.

```shell
ghtags sync:single <name>
```

This will get the latest tag from the database and update your tag file.

### Refreshing Tags

```shell
ghtags refresh
```

Use this command to remove all the tags from the database and get them again.

### Deleting Project

```shell
ghtags repo:delete <name>
```

This will delete the project from the database as well as all the tags associated with it in the database.


### Updating Project

```shell
ghtags repo:update <name>
```

This will update the project in the database.

## Contributing

We welcome contributions to GHTags. You can either fork the repo and make a pull request, or you can submit an issue or pull request.

## License

This software is licensed under the [MIT license](LICENSE).
