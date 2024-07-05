<?php

namespace App\Http\Controllers\Api\v1;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCompleteResource;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function me()
    {
        $user = $this->userRepositoryInterface->me();
        return ApiResponseClass::sendSuccessResponse(new UserCompleteResource($user), 'User retrieved successfully.');
    }

    public function update(UpdateUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->userRepositoryInterface->update($request->validated());
            DB::commit();
            return ApiResponseClass::sendSuccessResponse(new UserCompleteResource($user), 'User updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponseClass::sendErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            $this->userRepositoryInterface->destroy();
            DB::commit();
            return ApiResponseClass::sendSuccessResponse([], 'User deleted successfully.', Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            DB::rollBack();
            return ApiResponseClass::sendErrorResponse($e->getMessage(), $e->getCode());
        }
    }
}