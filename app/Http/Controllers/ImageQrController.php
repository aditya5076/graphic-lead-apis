<?php

namespace App\Http\Controllers;

use App\ImageQr;
use App\Traits\GenerateUUID;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
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

    function validImage($file)
    {
        $size = getimagesize($file);
        return (strtolower(substr($size['mime'], 0, 5)) == 'image' ? true : false);
    }

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
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'image' => 'required|array',
            'attributes' => 'nullable|array',
            'attributes.error_connection' => ['nullable', Rule::in(['L', 'M', 'Q', 'H'])],
            'attributes.quiet_zone' => ['nullable', Rule::in([0, 1, 2, 3, 4])],
            'attributes.version' => ['nullable', Rule::in([0, 1, 2, 3, 4, 5])],
            'attributes.rotate' => ['nullable', Rule::in([0, 90, 180, 270])],
            'attributes.eye_shape' => ['nullable', Rule::in(['rounded', 'sqaure'])],
        ]);

        if ($fields->fails()) return \response()->json(['errors' => $fields->errors()->messages()], 422);

        $image = new ImageQr;
        $image->imageid = $this->v4();
        $image->userid = \auth()->user()->userid;

        if (array_key_exists('method', $request->image)) {
            if ($request->image['method'] === 'url') {

                $url = $request->image['url'];

                if ($this->validImage($url)) {

                    $headers = \get_headers($url);
                    $contentType = $headers[\count($headers) - 1];
                    $imageType = \substr($contentType, 14);


                    $words = \explode('/', $imageType);
                    $ext = $words[1];

                    $img = $image->imageid . '.' . $ext;
                    // Function to write image into file
                    file_put_contents('images/' . $img, file_get_contents($url));
                    $image->contenttype = $imageType;
                    $image->submitted = \now();
                    $image->ttl = \now();
                    $image->status = 'OK';
                    $image->save();

                    Storage::put('json/ImageQRRequests/' . $image->imageid . '.json', \json_encode($request->attributes));
                    return \response()->json(['success' => "image is uploaded to the system. Here is the id {$image->imageid}"]);
                }
            } else {
                $image->save();
                Storage::put('json/ImageQRRequests/' . $image->imageid . '.json', \json_encode($request->attributes));
                return \response()->json([
                    'id' => $image->imageid,
                    'upload_url' =>  "https://" . $_SERVER['HTTP_HOST'] . "" . $_SERVER['REQUEST_URI'] . "/" . $image->imageid
                ], 201);
            }
        }
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
        // $fields = Validator::make($request->all(), [
        //     'photo' => 'required|mimes:jpeg,bmp'
        // ]);
        // if ($fields->fails()) return \response()->json(['errors' => $fields->errors()->messages()], 422);

        // \file_get_contents()

        // return $request->header('content-type');

        // $objInputStream = fopen("php://input", "rb");
        // $objSaveStream = fopen($uuid . "." . 'png', "wb");
        // // Storage::put('app/public/new', $objSaveStream);
        // stream_copy_to_stream($objInputStream, $objSaveStream);

        // echo "$uuid Received\n";


        // if ($image) {
        //     if ($request->hasFile('image')) {
        //         $fileNameWithExt = $request->file('image')->getClientOriginalName();
        //         $fileName = \pathinfo($fileNameWithExt, \PATHINFO_FILENAME);
        //         $ext = $request->file('image')->getClientOriginalExtension();
        //         $path = $request->file('image')->storeAs('images/', $uuid . '.' . $ext);

        //         $image->contenttype = 'image/' . $ext;
        //         $image->submitted = \now();
        //         $image->ttl = \now();
        //         $image->status = 'OK';
        //         $image->save();

        //         return \response()->json([
        //             'id' => $uuid,
        //             'size' => $request->file('image')->getSize(),
        //         ], 201);
        //     }
        // }



        $image = ImageQr::findOrFail($uuid);


        // strip off front slash
        // $uniqueID = substr($_SERVER['PATH_INFO'], 1);
        // $uniqueID =  json_decode(file_get_contents('php://input'),  true);

        // uniqueID required and this must be a PUT request
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            // Cakephp $this->request->contentType() ??
            $mimeType = $request->header('content-type');
            $fileExten = "";
            if ($mimeType == "image/jpeg" || $mimeType == "image/jpg") {
                $fileExten = "jpg";
            } elseif ($mimeType == "image/png") {
                $fileExten = "png";
            } else {
                throw new Exception("Error Processing Request", 1);
            }

            if ($fileExten) {
                $objInputStream = fopen("php://input", "rb");
                $objSaveStream = fopen('images/' . $uuid . "." . $fileExten, "wb");
                stream_copy_to_stream($objInputStream, $objSaveStream);


                $image->contenttype = $mimeType;
                $image->submitted = \now();
                $image->ttl = \now();
                $image->status = 'OK';
                $image->save();


                return \response()->json([
                    'success' => "$uuid Received",
                ], 201);
            } else {
                return \response()->json([
                    'error' => "something went wrong",
                ], 400);
            }
        } else {
            return \response()->json([
                'error' => "Not a PUT request",
            ], 400);
        }
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
            // $content = Storage::disk('public')->get($image->imageid . '.' . $ext);
            // return response($content)->header('content-type', $image->contenttype);
            $file = public_path('images\\') . $image->imageid . "." . $ext;
            $headers = array(
                "Content-Type: $image->contenttype",
            );

            $image->processed = \now();
            $image->save();

            return Response::download($file, "$image->imageid . '.' . $ext", $headers);
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
