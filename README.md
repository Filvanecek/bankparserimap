#Bank Email Parser

Php library for parse emails from czech banks over IMAP protocol built with OOP.

## Installation & Requirements

Application need third party IMAP library. 
The recommended way to install the IMAP library is through [Composer](https://getcomposer.org):

```
$ composer require ddeboer/imap
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

## Usage

### Connect and Authenticate

```php
use ImapBankParser\Parser;

$emails = new Parser(
    string 'imap.gmail.com',   // required
    string 'your@email.cz',    // required 
    string 'emailpassword',    // required
    string $port,              // defautl '993'
    string $flags,             // default '/imap/ssl/validate-cert'
    array $parameters          // default []
);
```

###Boxes

Default box for finding is INBOX, but you can set specific box in mail:

```
$emails->setBox('yourbox');
```

###Emails

#####All emails in box:

```
$emails->getEmails()->asArray();
```

#####Finding specific emails in box:

If you need only emails received from specific email address **you must create search expression**:

```
$search = new SearchExpression();
$search->addCondition(new From('searched@email.cz'));

$emails->setSearchExpression($search);
$emails->getEmails()->asArray();
```

You can combine search expression:

```
$search->addCondition(new Subject());
$search->addCondition(new Keyword());
...
```

All options can be found in ddeboer/imap library folder or [library GITHUB page](https://github.com/ddeboer/imap)

#####Parsing Emails

For parsing emails you must set bank parser:

```
$emails->setBank(Bank::FIOBANK);
$emails->getEmails()->parseEmails()->getParsed();
```

#####Deleting Emails

```
$emails->getEmails()->deleteEmails();
```
####Get parsed emails and delete

```
$emails->getEmails()->deleteEmails()->getParsed()->asArray();
```