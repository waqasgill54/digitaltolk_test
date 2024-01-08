<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;
use Illuminate\Support\Arr;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */


    // Move validation to a separate class or use Laravel validation features
    private function validateBooking(Request $request)
    {
        // Validation logic here
    }

    public function index(Request $request)
    {
        try{
            $this->validateBooking($request);
            // Instead of using env('ADMIN_ROLE_ID') and env('SUPERADMIN_ROLE_ID') directly in the code consider storing these values in a configuration file.
            if($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobs($user_id);
            }elseif($request->__authenticatedUser->user_type == config('roles.admin') || $request->__authenticatedUser->user_type == config('roles.superadmin')){
                $response = $this->repository->getAll($request);
            }
            return response($response);
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try{
            $response = $this->repository->with('translatorJobRel.user')->find($id);
            return response($response);
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try{
            $this->validateBooking($request);
            $data = $request->all();
            $response = $this->repository->store($request->__authenticatedUser, $data);
            return response($response);
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        try{
            $this->validateBooking($request);
            $data = $request->all();
            $cuser = $request->__authenticatedUser;
        // Use Arr::except instead of array_except as it deprecated
            $response = $this->repository->updateJob($id, Arr::except($data, ['_token', 'submit']), $cuser);
            return response($response);
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try{
            $this->validateBooking($request);
        // Remove unused variable if not needed
            // $adminSenderEmail = config('app.adminemail');
            return response($this->repository->storeJobEmail($request->all()));
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        try{
            $this->validateBooking($request);
            if($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobsHistory($user_id, $request);
                return response($response);
            }
            return null; // Consider returning a response with an appropriate status code instead of null
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try{
            $this->validateBooking($request);
            $data = $request->all();
            $user = $request->__authenticatedUser;
            $response = $this->repository->acceptJob($data, $user);
            return response($response);
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    public function acceptJobWithId(Request $request)
    {
        try{
            $this->validateBooking($request);
            $data = $request->get('job_id');
            $user = $request->__authenticatedUser;
            $response = $this->repository->acceptJobWithId($data, $user);
            return response($response);
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try{
            $this->validateBooking($request);
            $data = $request->all();
            $user = $request->__authenticatedUser;
            $response = $this->repository->cancelJobAjax($data, $user);
            return response($response);
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try{
            $this->validateBooking($request);
            return response($this->repository->endJob($request->all()));
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }

    }

    public function customerNotCall(Request $request)
    {
        try{
            $this->validateBooking($request);
            return response($this->repository->customerNotCall($request->all()));
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try{
            $this->validateBooking($request);
            $user = $request->__authenticatedUser;
            return response($this->repository->getPotentialJobs($user));
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    public function distanceFeed(Request $request)
    {
        try{
            $this->validateBooking($request);
            $data = $request->all();

        // Simplify variable assignments using the null coalescing operator
            $distance = $data['distance'] ?? "";
            $time = $data['time'] ?? "";
            $jobid = $data['jobid'] ?? "";
            $session = $data['session_time'] ?? "";
            $flagged = ($data['flagged'] == 'true') ? 'yes' : 'no';
            $manually_handled = ($data['manually_handled'] == 'true') ? 'yes' : 'no';
            $by_admin = ($data['by_admin'] == 'true') ? 'yes' : 'no';
            $admincomment = $data['admincomment'] ?? "";

        // Use Eloquent update method to update records
            if ($time || $distance) {
                Distance::where('job_id', '=', $jobid)->update(['distance' => $distance, 'time' => $time]);
            }

            if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
                Job::where('id', '=', $jobid)->update([
                    'admin_comments' => $admincomment,
                    'flagged' => $flagged,
                    'session_time' => $session,
                    'manually_handled' => $manually_handled,
                    'by_admin' => $by_admin
                ]);
            }

            return response('Record updated!');
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    public function reopen(Request $request)
    {
        try{
            $this->validateBooking($request);
            return response($this->repository->reopen($request->all()));
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    public function resendNotifications(Request $request)
    {
        try{
            $this->validateBooking($request);
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);
            if (!$job) {
            return response(['error' => 'Job not found'], 404); // HTTP 404 Not Found
            }
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');
            return response(['success' => 'Push sent']);
        } catch (\Exception $e) {
            // Handle exceptions
            // log exceptions either in database or filebase.
            return response()->json(['error' => 'Booking failed'], 500);
        }
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */

    public function resendSMSNotifications(Request $request)
    {
        $this->validateBooking($request);
        try {
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);

            if (!$job) {
            return response(['error' => 'Job not found'], 404); // HTTP 404 Not Found
        }

        $job_data = $this->repository->jobToData($job);
        $this->repository->sendSMSNotificationToTranslator($job);
        return response(['success' => 'SMS sent']);
    } catch (\Exception $e) {
        return response(['error' => $e->getMessage()], 500); // HTTP 500 Internal Server Error
    }
}

}
