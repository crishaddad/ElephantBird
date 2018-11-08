ElephantBird
=============

An attempt at a fully-featured and customisable script to copy the contents of
a Twitter account to a Mastodon account.

Features
--------
- Multiple Profiles
- Optional post/ignore Retweets
- Optional post/ignore @Replies
- Optionally rewrite @usernames to:
  @username@twitter.com 
  OR
  http://twitter.com/username
- Download and repost media (videos,images)
- Optionally post full video OR video thumbnail
- Append a string (e.g. hashtags) to all toots
- Optionally CW all toots with custom message 
- Set toot visibility
- Set accessiblity text for media
- Many advanced options for the adventurous...

Installation
------------
- Install dependencies:
``` bash
sudo apt-get install php php-curl php-sqlite3
```

- Clone repo 

``` bash
git clone http://gitlab.com/csa/ElephantBird.git ~/ElephantBird
```

- Go to the repo directory 
  `cd ~/ElephantBird`

- Create your twitter.php config and edit it with API details

``` bash
cp config/twitter.php.example twitter.php
vi config/twitter.php
```

- Create your mastodon.php config and edit it with API details

``` bash
cp config/mastodon.php.example mastodon.php
vi config/mastodon.php
```

- [Optional] Set up your application options 

``` bash 
vi config/options.php
vi config/advanced.php
```

- Run ElephantBird.php 

```bash
./ElephantBird.php
```

- Configure a cronjob as required e.g. 

``` bash
crontab -e
```

``` cron
# cron job to run every 30 minutes
*/30 * * * *  cd ~/ElephantBird && ./ElephantBird.php
```

Advanced Usage
--------------

Elephantbird can be configured with multiple profiles using command line
options to pass a profile directory: 

``` bash
./ElephantBird -c /path/to/profile/directory
```

A profile dir must contain a full complement of all 4 config files: 
  `twitter.php`
  `mastodon.php`
  `options.php`
  `advanced.php`

There are numerous other command line options that can be found with:
``` bash
./ElephantBird --help
```

Command line options passed at run time will override or augment config
in the profile dir, dependent on context:

- `--dry-run` can only FORCE a dry run, not disable it
- `--verbose` can only INCREASE verbosity, not disable it
- `--config` will ALWAYS OVERRIDE default config
- `--tweets` will ALWAYS OVERRIDE configured value

Wishlist
--------
- Expand Links to show full URL instead of t.co
- Enable/Disable appending link to original tweet
- Optionally post remote media link, instead of reposting image
- Sync profile picture and header image
- Allow for followers to enable spoiler warnings for 24 hours 
  by sending an @ message
- Select video resolution
- Delete old toots after a set period

Credits
-------
Thanks to numerous Stack Overflow posters for sharing code snippets that were
tremendously helpful:
- [Anubhava Srivastava for URL formatting](https://stackoverflow.com/a/6427654)
- [Christian Joudrey for URL expansion](https://stackoverflow.com/a/4495720)
- [Rivers for Twitter/Oauth code](https://stackoverflow.com/a/12939923)

License
-------
ElephantBird is licensed under GPLv3. 

  "Be excellent to each other."
   - Bill S. Preston, Esq. & "Ted" Theodore Logan
