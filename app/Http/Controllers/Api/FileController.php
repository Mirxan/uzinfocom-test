<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Http\Requests\FileRequest;
use App\Interfaces\FileRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function __construct(private FileRepositoryInterface $fileInterface)
    {
        $this->fileInterface = $fileInterface;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): array
    {
        return response()->successResponse($this->fileInterface->getFiles($request->all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileRequest $request): array
    {
        return response()->successResponse($this->fileInterface->createFile($request->validated()));
    }

    /**
     * MultipleStore a newly created resource in storage.
     */
    public function multipleStore(FileRequest $request): array
    {
        return response()->successResponse($this->fileInterface->createMultipleFiles($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $unique_key, string $filename): BinaryFileResponse
    {
        return response()->file($this->fileInterface->getFile($unique_key, $filename));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): array
    {
        return response()->successResponse($this->fileInterface->deleteFile($id));
    }
}
