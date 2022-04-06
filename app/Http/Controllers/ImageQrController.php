<?php

namespace App\Http\Controllers;

use App\ImageQr;
use App\Traits\GenerateUUID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImageQrController extends Controller
{
    /**
     * @OA\Get(
     *     path="/projects",
     *     @OA\Response(response="200", description="Display a listing of projects.")
     * )
     */
    use GenerateUUID;

    /**
     * @OA\Post(
     *      path="api/v1/upload",
     *      operationId="generateUUID",
     *      tags={"Post/ImageQr"},
     *      summary="Get UUID",
     *      description="Returns UUID",
     *      @OA\Parameter(
     *          name="error_connection",
     *          in="query",
     *          @OA\Schema(
     *           type="char"
     *           )
     *       ),
     *      @OA\Parameter(
     *          name="quiet_zone",
     *          in="query",
     *          @OA\Schema(
     *           type="int"
     *           )
     *       ),
     *      @OA\Parameter(
     *          name="version",
     *          in="query",
     *          @OA\Schema(
     *           type="int"
     *           )
     *       ),
     *      @OA\Parameter(
     *          name="rotate",
     *          in="query",
     *          @OA\Schema(
     *           type="int"
     *           )
     *       ),
     *      @OA\Parameter(
     *          name="eye_shape",
     *          in="query",
     *          @OA\Schema(
     *           type="string"
     *           )
     *       ),
     *      @OA\Response(
     *          response=201,
     *          description="Returns the uuid",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function upload(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'error_connection' => ['nullable', Rule::in(['L', 'M', 'Q', 'H'])],
            'quiet_zone' => ['nullable', Rule::in([0, 1, 2, 3, 4])],
            'version' => ['nullable', Rule::in([0, 1, 2, 3, 4, 5])],
            'rotate' => ['nullable', Rule::in([0, 90, 180, 270])],
            'eye_shape' => ['nullable', Rule::in(['rounded', 'sqaure'])],
        ]);

        if ($fields->fails()) return \response()->json(['errors' => $fields->errors()->messages()], 422);

        // $qrCodeAttributes=[
        //     'error_connection'=>$request->has('error_connection')
        // ]

        $image = new ImageQr;
        $image->imageid = $this->v4();
        $image->userid = \auth()->user()->userid;
        $image->save();

        Storage::put('json/ImageQRRequests/' . $image->imageid . '.json', \json_encode($request->all()));

        return \response()->json([
            'id' => $image->imageid,
            'upload_url' => 'http://localhost:8000/api/v1/imageqr/' . $image->imageid
        ], 201);
    }


    /**
     * @OA\PUT(
     *      path="api/v1/imageqr/{uuid}",
     *      operationId="ImageUpload",
     *      tags={"ImageUpload"},
     *       security={{"bearerAuth":{}}},
     *      summary="gets the UUID and size",
     *      description="Returns UUID and size",
     *  @OA\Parameter(
     *          name="image",
     *          in="query",
     *          required=true,
     *         @OA\MediaType(
     *           mediaType="multipart/form-data",
     *      )
     *       ),
     *   @OA\Examples(
     *        summary="VehicleStoreEx1",
     *        example = "VehicleStoreEx1",
     *       value = {
     *           "name": "vehicle 1"
     *         },
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Returns the image-uuid and size",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function update($uuid, Request $request)
    {
        $fields = Validator::make($request->all(), [
            'image' => 'required|mimes:jpeg,png'
        ]);

        if ($fields->fails()) return \response()->json(['errors' => $fields->errors()->messages()], 422);

        $image = ImageQr::where('imageid', $uuid)->where('userid', \auth()->user()->userid)->first();

        if ($image) {
            if ($request->hasFile('image')) {
                $fileNameWithExt = $request->file('image')->getClientOriginalName();
                $fileName = \pathinfo($fileNameWithExt, \PATHINFO_FILENAME);
                $ext = $request->file('image')->getClientOriginalExtension();
                $path = $request->file('image')->storeAs('images/', $uuid . '.' . $ext);

                $image->contenttype = 'image/' . $ext;
                $image->submitted = \now();
                $image->ttl = \now();
                $image->status = 'OK';
                $image->save();

                return \response()->json([
                    'id' => $uuid,
                    'size' => $request->file('image')->getSize(),
                ], 201);
            }
        }

        return \response()->json([
            'error' => 'Something went wrong'
        ], 401);
    }


    /**
     * @OA\GET(
     *      path="/api/v1/url/{uuid}",
     *      operationId="Get Image",
     *      tags={"/imageqr/{id}"},
     *      security={
     *          {"token": {}},
     *      },
     *      summary="gets the actual image",
     *      description="Returns UUID and size",
     *      @OA\Response(
     *          response=201,
     *          description="Returns the image-file with content-type",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error- Content-Type: application/json"
     *      )
     *     )
     */
    public function show($uuid)
    {
        $image = ImageQr::where('imageid', $uuid)->where('userid', \auth()->user()->userid)->first();
        if ($image) {
            $words = \explode('/', $image->contenttype);
            $ext = $words[1];
            $content = Storage::get('images/' . $image->imageid . '.' . $ext);
            return response($content)->header('Content-Type', $image->contenttype);
        }
        return \response()->json([
            'error' => 'Something went wrong'
        ], 401);
    }

    /**
     * @OA\GET(
     *      path="/api/v1/imagequeue",
     *      operationId="Get Images",
     *      tags={"GET /imagequeue"},
     *      security={
     *          {"token": {}},
     *      },
     *      summary="Retrieve a list of all images queued for QR code generation",
     *      description="Returns the output of a MySQL query (ie: SELECT id, content-type, submitted, processed, status, ttl FROM imagequeue AS iq, users AS u WHERE iq.userid=u.userid)",
     *      @OA\Response(
     *          response=201,
     *          description="Retrieve a list of all images queued for QR code generatio",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error- Content-Type: application/json"
     *      )
     *     )
     */
    public function index()
    {
        $user = \auth()->user();
        $images = ImageQr::where('userid', $user->userid)->get();
        if (\count($images) > 0) return \response()->json(['data' => $images], 200);

        return \response()->json(['errors' => 'something went wrong'], 422);
    }
}
