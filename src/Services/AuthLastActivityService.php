<?php
namespace DevMoez\AuthLastActivity\Services;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use DevMoez\AuthLastActivity\Actions\CreateAuthLastActivityAction;
use DevMoez\AuthLastActivity\Actions\ListAuthLastActivityAction;
use DevMoez\AuthLastActivity\Actions\DeleteAuthLastActivityAction;

class AuthLastActivityService {
    
    public function list(): JsonResponse
    {
        try {
            $data = (new ListAuthLastActivityAction())->execute();

            return response()->json([
                'success' => true,
                'data' => $data
            ], Response::HTTP_OK);

        } catch (Exception $exception) {
            logger()->error($exception);

            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function create(Request $request): JsonResponse
    {
        try {
            $data = (new CreateAuthLastActivityAction($request))->execute();
            return response()->json([
                'success' => true,
                'message' => 'User last activity logged.',
                'data' => $data
            ], Response::HTTP_OK);

        } catch (Exception $exception) {
            logger()->error($exception);

            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function delete(int $id): JsonResponse
    {
        try {
            $data = (new DeleteAuthLastActivityAction($id))->execute();
            return response()->json([
                'success' => true,
                'message' => 'User last activity has been deleted.',
                'data' => $data
            ], Response::HTTP_OK);

        } catch (Exception $exception) {
            logger()->error($exception);

            return response()->json([
                'success' => false,
                'error' => $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}