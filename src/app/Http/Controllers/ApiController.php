<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use App\Subscriber;
use Illuminate\Http\Request;
use App\Jobs\SendTelegramMessage;
use App\Services\ValidationService;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    const IDS_DELIMITER = ';';

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $subscribers = Subscriber::orderByDesc('id')
            ->paginate(
                $request->get('per_page', config('app.api.per_page', 5))
            );
        return response()->json($subscribers);
    }

    /**
     * Send message to subscribers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $ids = $request->get('ids', []);
        $message = trim($request->get('message', ''));
        Log::info(__METHOD__, [
            'ids' => $ids,
            'message' => $message
        ]);
        $validationService = new ValidationService();
        $validationService->validate([
            'ids' => $ids,
            'message' => $message,
            'request' => $request->all()
        ], [
            'ids' => 'required|array',
            'message' => 'required|string'
        ]);
        if ($validationService->fails()) {
            return $this->failedResponse('Validation errors', 422, $validationService->getMessages());
        }
        $telegramUserIds = Subscriber::whereIn('id', $ids)
            ->whereType(Subscriber::TYPE_USER)
            ->pluck('telegram_id')
            ->toArray();
        $telegramBotService = new TelegramBotService();
        $telegramBotService->send($telegramUserIds, $message);
        return $this->successResponse('Message has been sent');
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsubscribe(Request $request)
    {
        $ids = $this->splitIds($request->get('ids', ''));
        if (empty($ids) || !is_array($ids)) {
            return $this->failedResponse('Nothing to delete');
        }
        $count = Subscriber::whereIn('id', $ids)
            ->delete();
        if (!$count) {
            return $this->failedResponse('Records to deleting not found', 404);
        }
        return $this->successResponse('Subscriber has been deleted', 202);
    }

    /**
     * TODO: remove on prod.
     * Seed fake subscribers
     * @return \Illuminate\Http\JsonResponse
     */
    public function populate()
    {
        $count = 10;
        \Illuminate\Support\Facades\Artisan::call('app:populate_subscribers', [
            '--count' => $count
        ]);
        return $this->successResponse("$count subscribers added in DB");
    }

    /**
     * TODO: remove on prod.
     * Force remove all generated subscribers
     * @return \Illuminate\Http\JsonResponse
     */
    public function clean()
    {
        \Illuminate\Support\Facades\Artisan::call('app:clear_subscribers');
        return $this->successResponse("Generated subscribers have been removed from DB");
    }

    /**
     * Response on success
     *
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    private function successResponse(string $message, int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], $code);
    }

    /**
     * Response on filed
     *
     * @param string $message
     * @param int $code
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    private function failedResponse(string $message, int $code = 400, array $errors = [])
    {
        $result = [
            'success' => false,
            'message' => $message,
        ];
        if (!empty($errors)) {
            $result['errors'] = $errors;
        }
        return response()->json($result, $code);
    }

    /**
     * Split string by delimiter for transformation to array
     * @param string $ids
     * @return array
     */
    private function splitIds(string $ids): array
    {
        $idsArr = explode(self::IDS_DELIMITER, $ids);
        $idsArr = array_filter($idsArr, function ($value) {
            return !empty($value);
        });
        return $idsArr;
    }
}
