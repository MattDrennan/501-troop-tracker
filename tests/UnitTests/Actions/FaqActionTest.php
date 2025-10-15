<?php

declare(strict_types=1);

namespace Tests\UnitTests\Actions;

use App\Actions\FaqAction;
use App\Domain\Services\AuthenticationService;
use App\Domain\Responses\LoginResponse;
use App\Requests\LoginRequest;
use App\Responders\HtmlResponder;
use App\Responders\RedirectResponder;
use App\Results\HtmlResult;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FaqActionTest extends TestCase
{
    private MockObject|HtmlResponder $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = $this->createMock(HtmlResponder::class);
    }



    public function testItShowsForOnGetRequest(): void
    {
        //  arrange
        $action = $this->createFaqAction();

        //  act
        $result = $action->execute();

        //  assert
        $this->assertInstanceOf(HtmlResult::class, $result);
    }

    private function createFaqAction(): FaqAction
    {
        return new FaqAction($this->responder);
    }
}