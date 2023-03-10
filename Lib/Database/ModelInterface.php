<?php

namespace Lib\Database;

interface ModelInterface
{
    public function getData(): array;
    public function getEntity(): string;
}