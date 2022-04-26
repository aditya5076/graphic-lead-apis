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
use Symfony\Component\Process\Process;

class ImageQrController extends Controller
{

    // SHELL COMMANDS
    public function execute($cmd)
    {
        $process = Process::fromShellCommandline($cmd);

        $processOutput = '';

        $captureOutput = function ($type, $line) use (&$processOutput) {
            $processOutput .= $line;
        };

        $process->setTimeout(null)
            ->run($captureOutput);

        $processCode = $process->getExitCode();

        $errorMessage = \null;
        if ($process->getExitCode()) {
            $exception = new Exception($cmd . " - " . $processOutput);
            report($exception);

            $errorMessage = $exception;
        }

        // return $processOutput;
        return ['process_code' => $processCode, 'process_output' => $processOutput];
    }
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
            'output' => 'nullable|array',
            'data' => 'required',
            'attributes.error_connection' => ['nullable', Rule::in(['L', 'M', 'Q', 'H'])],
            'attributes.quiet_zone' => ['nullable', Rule::in([0, 1, 2, 3, 4])],
            'attributes.version' => ['nullable', Rule::in([0, 1, 2, 3, 4, 5])],
            'attributes.rotate' => ['nullable', Rule::in([0, 90, 180, 270])],
            'attributes.eye_shape' => ['nullable', Rule::in(['rounded', 'sqaure'])],
            'output.format' => ['nullable', Rule::in(['jpeg', 'png', 'jpg'])],
            'output.callback_success' => 'nullable|string',
            'output.callback_failure' => 'nullable|string',
        ]);

        if ($fields->fails()) {
            return \response()->json(['detail' => $fields->errors()->messages()], 422);
        }


        $image = new ImageQr;
        $image->imageid = $this->v4();
        $image->userid = \auth()->user()->userid;

        $outputs = $request->json('output');
        $attributes = $request->json('attributes');
        $attributes['data'] = $request->data;
        if (array_key_exists('error_connection', $attributes)) {
            $attributes['ecl'] = $attributes['error_connection'];
            unset($attributes['error_connection']);
        }
        // $attributes['callback_success'] = "https://" . $_SERVER['HTTP_HOST'] . "" . '/backend-notify/' . $image->imageid . '?status=success';
        // $attributes['callback_failure'] = "https://" . $_SERVER['HTTP_HOST'] . "" . '/backend-notify/' . $image->imageid . '?status=failure';
        $attributes['callback_success'] = url('/') . '/backend-notify/' . $image->imageid . '?status=success';
        $attributes['callback_failure'] = url('/') . '/backend-notify/' . $image->imageid . '?status=failure';

        if (array_key_exists('method', $request->image)) {
            if ($request->image['method'] === 'url') {

                if (empty($request->image['url']) && \trim($request->image['url']) != '') return \response()->json(['detail' => ['msg' => ['transfer method is URL but no url provided']]], 422);

                $url = $request->image['url'];

                if (!@getimagesize($url)) return \response()->json(['detail' => ['msg' => 'image url is invalid']], 422);

                if ($this->validImage($url)) {

                    $headers = \get_headers($url);
                    $contentType = $headers[\count($headers) - 1];
                    $imageType = \substr($contentType, 14);


                    $words = \explode('/', $imageType);
                    $ext = $words[1];

                    $img = $image->imageid . '.' . $ext;
                    // Function to write image into file
                    file_put_contents(\storage_path('images/') . $img, file_get_contents($url));
                    $image->contenttype = $imageType;
                    $image->submitted = \now();
                    $image->ttl = \now()->addHours(24);
                    $image->status = 'processing';
                    $image->callback_success = isset($outputs) && \array_key_exists('callback_success', $outputs) ? $outputs['callback_success'] : \null;
                    $image->callback_failure =  isset($outputs) &&  \array_key_exists('callback_failure', $outputs) ? $outputs['callback_failure'] : \null;
                    $image->save();

                    // $this->executeCmd("scp -o StrictHostKeyChecking=no /var/www/html/path-to-image/c7aaf404-e740-4ded-996c-30766bda4012.jpg /var/www/html/path-to-image/c7aaf404-e740-4ded-996c-30766bda4012.json submit@stage1-1.intranet.graphiclead.com:process/");

                    // STORE INPUTS IN JSON
                    Storage::put($image->imageid . '.json', \json_encode($attributes));

                    //EXTERNAL COMMANDS
                    $imageFilePath = \storage_path('images/') . $image->imageid . "." . $ext;
                    $imageJsonPath = \storage_path('images/') . $image->imageid . ".json";

                    // scp -o StrictHostKeyChecking=no $imageFilePath $imageJsonPath submit@stage1-1.intranet.graphiclead.com:process/
                    $resultCode = $this->execute("scp -o StrictHostKeyChecking=no $imageFilePath $imageJsonPath submit@stage1-1.intranet.graphiclead.com:process/");

                    if (\count($resultCode) && $resultCode['process_code']) {
                        // $image->status = 'Failed to send image backend';
                        $image->status = $resultCode['process_output'];
                        $image->save();
                        return \response()->json(['detail' => ['msg' => 'Failed to send image backend']], 500);
                    }

                    $resultCode2 = $this->execute("ssh -o StrictHostKeyChecking=no  submit@stage1-1.intranet.graphiclead.com bin/submit-job.py -–load-json /home/submit/process/$image->imageid.json -–input_image /home/submit/process/$image->imageid.$ext");

                    if (\count($resultCode2) && $resultCode2['process_code']) {
                        // $image->status = 'Failed to start image backend processing';
                        $image->status = $resultCode2['process_output'];

                        $image->save();
                        return \response()->json(['detail' => ['msg' => 'Failed to start image backend processing']], 500);
                    }

                    $response['id'] = $image->imageid;
                    $response['image_size'] = \round(\filesize(\storage_path('images/') . $image->imageid . "." . $ext));
                    return \response()->json($response);
                }
            } else {
                $image->ttl = \now()->addHours(24);
                $image->status = 'waiting for upload';
                $image->callback_success = isset($outputs) && \array_key_exists('callback_success', $outputs) ? $outputs['callback_success'] : \null;
                $image->callback_failure = isset($outputs) && \array_key_exists('callback_failure', $outputs) ? $outputs['callback_failure'] : \null;
                $image->save();
                Storage::put($image->imageid . '.json', \json_encode($attributes));
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

        $image = ImageQr::find($uuid);

        if (!$image)  return \response()->json(['detail' => ['msg' => 'Not Found']], 404);


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
                // throw new Exception("Error Processing Request", 1);
                return \response()->json(['detail' => ['msg' => 'Error Processing Request']], 400);
            }

            if ($fileExten) {
                $objInputStream = fopen("php://input", "rb");
                $objSaveStream = fopen(\storage_path('images/') . $uuid . "." . $fileExten, "wb");
                stream_copy_to_stream($objInputStream, $objSaveStream);

                $image->contenttype = $mimeType;
                $image->submitted = \now();
                $image->ttl = \now()->addHours(24);
                $image->status = 'processing';
                $image->save();

                //EXTERNAL COMMANDS
                // return $storedImagePath = $appPath . \storage_path('images/') . $uuid . "." . $fileExten;

                $imageFilePath = \storage_path('images/') . $uuid . "." . $fileExten;
                $imageJsonPath = \storage_path('images/') . $uuid . ".json";

                $resultCode = $this->execute("scp -o StrictHostKeyChecking=no $imageFilePath $imageJsonPath submit@stage1-1.intranet.graphiclead.com:process/");

                if (\count($resultCode) && $resultCode['process_code']) {
                    // $image->status = 'Failed to send image backend';
                    $image->status = $resultCode['process_output'];
                    $image->save();
                    return \response()->json(['detail' => ['msg' => 'Failed to send image backend']], 500);
                }

                $resultCode2 = $this->execute("ssh -o StrictHostKeyChecking=no  submit@stage1-1.intranet.graphiclead.com bin/submit-job.py -–load-json /home/submit/process/$uuid.json -–input_image /home/submit/process/$uuid.$fileExten");

                if (\count($resultCode2) && $resultCode2['process_code']) {
                    // $image->status = 'Failed to send image backend';
                    $image->status = $resultCode2['process_output'];
                    $image->save();
                    return \response()->json(['detail' => ['msg' => 'Failed to start image backend processing']], 500);
                }


                return \response()->json([
                    'id' => $image->imageid,
                    'image_size' => \round(\filesize(\storage_path('images/') . $uuid . "." . $fileExten))
                ], 201);
            } else {
                return \response()->json(['detail' => [
                    'msg' => "something went wrong",
                ]], 400);
            }
        } else {
            return \response()->json(['detail' => [
                'msg' => "something went wrong",
            ]], 400);
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

            if ($image->processed) {
                $words = \explode('/', $image->contenttype);
                $ext = $words[1];
                $file = \storage_path('images\\') . $image->imageid . "." . $ext;
                $headers = array(
                    "Content-Type: $image->contenttype",
                );

                // $image->processed = \now();
                $image->save();
                return Response::download($file, "$image->imageid . '.' . $ext", $headers);
            } else {
                return \response()->json(['detail' => [
                    'msg' => $image->contenttype
                ]], 422);
            }
        }
        return \response()->json(['detail' => [
            'msg' => "something went wrong",
        ]], 400);
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

        return \response()->json(['detail' => ['msg' => 'something went wrong']], 422);
    }

    public function backendNotify(Request $request, $uuid)
    {
        $image = ImageQr::find($uuid);
        if (!$image) return \response()->json(['detail' => ['msg' => 'Not Found']], 404);
        $image->ttl = \now()->addHours(24);

        if ($request->query('status')) {

            if ($request->query('status') == 'failure') {
                $image->status = 'Failed';
                $image->save();
                return \response()->json(['detail' => ['msg' => 'Failed']], 500);
            } else if ($request->query('status') == 'success') {

                $words = \explode('/', $image->contenttype);
                $ext = $words[1];

                $imageFilePath = \storage_path('images/') . $image->imageid . "." . $ext;
                $imageJsonPath = \storage_path('images/') . $image->imageid . ".json";

                $resultCode = $this->execute("scp -o StrictHostKeyChecking=no $imageFilePath $imageJsonPath submit@stage1-1.intranet.graphiclead.com:process/");

                if (\count($resultCode) && $resultCode['process_code']) {
                    // $image->status = 'Failed to send image backend';
                    $image->status = $resultCode['process_output'];
                    $image->save();
                    if (isset($image->callback_failure)) {
                        $response = Http::get($image->callback_failure);
                    } else {
                        return \response()->json(['detail' => ['error' => 'Failed to retrieve image from backend server']], 500);
                    }
                } else if ($resultCode == 0) {
                    $image->processed = \now();
                    $image->save();
                    if (isset($image->callback_success)) {
                        $response = Http::get($image->callback_success);
                    }
                }
            }

            return \response()->json(['id' => $uuid]);
        }
    }
}