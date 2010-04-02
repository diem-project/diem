<?php

echo _tag('strong', $dmSentMail->subject);

echo _tag('div.mt10', nl2br($dmSentMail->body));