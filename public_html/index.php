<?php

use Rizen\Core\Input\FileInput;
use Rizen\Core\Stream\TokenStream;
use Rizen\Core\Token\TokenType;

include '../vendor/autoload.php';

function isAction(string $action): bool
{
    return $action === ($_GET['action'] ?? '');
}

function printTokenStream(TokenStream $stream, bool $onlyBody = false)
{
    if (!$onlyBody) {
        echo '<b>' . $stream->peek()->getSourceString()->getContextName() . '</b>';
        echo '<pre class="source-container" contenteditable="true">';
    }
    while (!$stream->eof()) {
        $token = $stream->next();

        $content = $token->getSourceString()->getContent();

        if ($token->getTokenType() === TokenType::String) {
            $delimiter = $token->getSourceString()->getMetadata()['delimiter'];
            $content = $delimiter . $content . $delimiter;
        }

        echo sprintf(
            '<span class="token %s" data-meta="%s">%s</span>',
            $token->getTokenType()->getHtmlClass(),
            htmlentities(
                json_encode([
                    'lineStart' => $token->getSourceString()->getRange()->getStart()->row,
                    'lineEnd' => $token->getSourceString()->getRange()->getEnd()->row,
                    'columnStart' => $token->getSourceString()->getRange()->getStart()->column,
                    'columnEnd' => $token->getSourceString()->getRange()->getEnd()->column,
                ])
            ),
            $content
        );
    };
    if (!$onlyBody) {
        echo '</pre>';
    }
}

$lexer = new \Rizen\RizenLexer();

if (isAction('lex')) {
    $code = $_POST['code'] ?? '';
    try {
        $stream = $lexer->lex($code, 'ajax::code');
        printTokenStream($stream, true);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    die();
}

header('content-type: text/html; charset=utf8');
?>
<!doctype>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lexing test</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/main.css?<?= time() ?>">
</head>
<body>
<div class="container">
    <?php

    foreach (new FilesystemIterator('../examples/', FilesystemIterator::SKIP_DOTS) as $file) {
        if ($file->getExtension() === 'rz') {
            printTokenStream($lexer->lex(new FileInput($file->getPathname())));
        }
    }

    ?>
</div>
<script>
    const editor = document.getElementsByClassName('source-container').item(0)

    class QueueItem {
        constructor(parent, id, content) {
            this.parent = parent
            this.id = id
            this.content = content
            this.caret = {
                line: JSON.parse(document.getSelection().anchorNode.parentElement.getAttribute('data-meta')).lineStart,
                column: document.getSelection().anchorOffset,
            }

            console.log('Created QueueItem with id: ', id)
            this.createRequest()
        }

        createRequest() {
            const params = new URLSearchParams()
            params.append('code', this.content)

            this.request = fetch(window.location + '?action=lex', {
                method: 'POST',
                body: params,
            })

            this.request.then((r) => r.text()).then(t => this.handleResponse(t))
        }

        handleResponse(response) {
            this.parent.notify('QueueItemDone', response, this)
        }
    }

    class RequestQueue {
        constructor() {
            this.queue = []
            this.listeners = {}
            this.ids = 0


            this.on('QueueItemDone', (r, q) => this.handleQueueItemDone(r, q))
        }

        push(content) {
            const id = this.ids++
            this.queue[id] = new QueueItem(this, id, content)
        }

        /**
         * @param {string} content
         * @param {QueueItem} q
         */
        handleQueueItemDone(content, q) {
            if (q.id < this.getLatestQueueItemId()) {
                return
            }

            this.notify('done', content, q.caret)
            this.queue = [] // reset queue
        }

        getLatestQueueItemId() {
            return this.queue.length === 0 ?
                0 : this.queue.length - 1
        }

        on(event, callback) {
            if (!this.listeners.hasOwnProperty(event)) {
                this.listeners[event] = []
            }
            this.listeners[event].push(callback)
        }

        notify(event, ...args) {
            if (!this.listeners.hasOwnProperty(event)) {
                console.log('no listeners for event: ', event)
                return
            }

            console.log('firing event ', event, ' for ', this.listeners[event].length, ' listeners')
            this.listeners[event].forEach(cb => cb(...args))
        }
    }

    /*
    const queue = new RequestQueue()
    let timeoutId = 0
    let currentContent = editor.innerText
    editor.addEventListener('keydown', function (e) {
        if (currentContent === editor.innerText) {
            return
        }
        console.log('querying new job')
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => {
            console.log('pushing job to queue')
            queue.push(editor.innerText, e)
        }, 250)
    })

    queue.on('done', (content, caret) => {
        editor.innerHTML = content
        currentContent = editor.innerText

        console.log('caret', caret)
        const sel = window.getSelection()
        const range = document.createRange();
        range.setStart(editor, caret.line)
        range.collapse(true)
        sel.removeAllRanges()
        sel.addRange(range)
        editor.focus();
    })
     */
</script>
</body>
</html>

