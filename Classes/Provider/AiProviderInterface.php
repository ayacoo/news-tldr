<?php

namespace Ayacoo\NewsTldr\Provider;

interface AiProviderInterface
{
    public function summarize(string $content): string;
}
