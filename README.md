# TYPO3 Extension news-tldr

## 1 Features

The extension makes it possible to create short news summaries using ChatGPT.

## 2 Hints

- The extension is only a proof of concept and was tested in the environment of
  TYPO3 version 12 and PHP 8.1.
- This code is experimental
- The code can be used and further developed as desired, e.g. for a backport to
  version 11
- Only the field bodytext is read. Other linked content elements must be read and
  manipulated via an event.

## 3 Usage

### 3.1 Prerequisites

To use this extension, you need the following requirements:

- PHP version 8.1 or higher
- TYPO3 version 12
- [News][3] Extension 11 or higher
- [ChatGPT API Token][2]

### 3.2 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your [Composer][1] based TYPO3 project:

```
composer require ayacoo/news-tldr
```

### 3.3 Event / EventListener

To modify the ChatGPT request, e.g. to change the number of characters, there is
an event: `ModifyChatGptContentEvent`.

### EventListener registration

```
services:
  Vendor\Ext\Listener\ContentListener:
    tags:
      - name: event.listener
        identifier: 'news-tldr/content'
        method: 'setContent'
        event: Ayacoo\NewsTldr\Event\ModifyChatGptContentEvent
```

### EventListener

```
<?php
declare(strict_types=1);

namespace Vendor\Ext\Listener;

use Ayacoo\NewsTldr\Event\ModifyChatGptContentEvent;

class ContentListener
{
    public function setContent(ModifyChatGptContentEvent $event): ModifyChatGptContentEvent
    {
        $row = $event->getRow()
        $text = strip_tags($row['bodytext'] ?? '');

        $content = 'Fasse mir diesen Text in 100 Zeichen zusammen: ' . $text;
        $event->setContent($content);

        return $event;
    }
}

```

## 4 Support

If you are happy with the extension and would like to support it in any way, I
would appreciate the support of social institutions.

[1]: https://getcomposer.org/

[2]: https://platform.openai.com/docs/guides/chat

[3]: https://github.com/georgringer/news
