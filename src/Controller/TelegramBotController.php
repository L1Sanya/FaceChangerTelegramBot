<?php

namespace App\Controller;

use App\Service\ReplicateService;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Telegram\Bot\Api;
use Symfony\Component\HttpFoundation\RequestStack;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Update;

class TelegramBotController
{
    private Api $telegram;
    private ReplicateService $replicateService;
    private RequestStack $requestStack;

    /**
     * @param Api $telegram
     * @param ReplicateService $replicateService
     * @param RequestStack $requestStack
     * @throws TelegramSDKException
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->telegram = new Api(Env::get('TELEGRAM_BOT_TOKEN'));
        $this->replicateService = new ReplicateService(Env::get('REPLICATE_API_TOKEN'));
        $this->requestStack = $requestStack;
    }

    /**
     * @throws TelegramSDKException
     */

    #[Route('/telegram', name: 'telegram', methods: ['GET'])]
    public function setWebhook() : Response
    {
        $url = $this->requestStack->getCurrentRequest()->getUri() . '/api/bot';
        $url = str_replace('http://', 'https://', $url);
        return new Response($this->telegram->getWebhookInfo());
    }

    #[Route('/telegram/api/bot', name: 'telegram.webhook', methods: ['POST'])]
    public function webhook(Request $request): JsonResponse
    {
        $update = new Update($request->toArray());
        $this->telegram->sendMessage(['chat_id' => $update->message->chat->id, 'text' => $update->message->text]);
        return new JsonResponse(['status' => 'ok']);
    }

}
