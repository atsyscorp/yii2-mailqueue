<?php

namespace atsyscorp\Mailqueue\Tests\Unit;

use PHPUnit\Framework\TestCase;
use atsyscorp\mailqueue\MailQueue;

class MiClaseTest extends TestCase
{
    public function testMetodoEjemplo()
    {
        $instancia = new MailQueue();
        $this->assertEquals('resultado esperado', $instancia->metodoEjemplo());
    }
}