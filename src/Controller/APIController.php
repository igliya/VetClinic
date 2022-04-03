<?php

namespace App\Controller;

use App\Repository\CheckupRepository;
use App\Repository\UserRepository;
use App\Service\APIService;
use App\Service\JWTService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class APIController extends AbstractController
{
    /**
     * @Route("/login", name="doctor_login", methods={"POST"})
     */
    public function doctorLogin(Request $request, APIService $apiService, JWTService $jwtService): Response
    {
        try {
            $body = json_decode($request->getContent(), true);
            // validate user credentials
            if (empty($body['login']) || empty($body['password'])) {
                throw new BadRequestException('Invalid credentials');
            }
            $login = $body['login'];
            $password = $body['password'];
            $doctor = $apiService->checkUserCredentials($login, $password);

            return $this->json([
                'access_token' => $jwtService->createToken($doctor->getId()),
                'full_name' => $doctor->getFullName(),
                'phone' => $doctor->getPhone(),
            ]);
        } catch (BadRequestException $badRequestException) {
            return $this->json([
                'code' => 400,
                'message' => $badRequestException->getMessage(),
            ], 400);
        } catch (NotFoundHttpException $notFoundHttpException) {
            return $this->json([
                'code' => 404,
                'message' => $notFoundHttpException->getMessage(),
            ], 404);
        }
    }

    /**
     * @Route("/checkups", name="doctor_checkups", methods={"GET"})
     */
    public function doctorCheckups(
        Request $request,
        UserRepository $userRepository,
        CheckupRepository $checkupRepository,
        JWTService $jwtService
    ): Response {
        try {
            $requestToken = $request->headers->get('Authorization');
            if (null === $requestToken) {
                throw new UnauthorizedHttpException('', 'You must provide Authorization header with Bearer token');
            }
            // check token
            $tokenParts = explode(' ', $requestToken);
            if (2 !== count($tokenParts) || 'Bearer' !== $tokenParts[0]) {
                throw new UnauthorizedHttpException('', 'You must provide Authorization header with Bearer token');
            }
            // validate token
            $jwtService->validateToken($tokenParts[1]);
            $token = $jwtService->parseToken($tokenParts[1]);

            // get doctor id
            $doctorId = (int) $token->claims()->get('user_id');
            // get doctor
            $doctor = $userRepository->find($doctorId);
            if (null === $doctor) {
                throw new NotFoundHttpException('Doctor not found');
            }
            $checkups = $checkupRepository->getDoctorCheckups($doctor);
            $checkupsDto = [];
            foreach ($checkups as $checkup) {
                $checkupsDto[] = [
                    'id' => $checkup->getId(),
                    'client' => $checkup->getPet()->getOwner()->getAccount()->getFullName(),
                    'pet' => $checkup->getPet()->getName(),
                    'date' => $checkup->getDate(),
                ];
            }
            return $this->json($checkupsDto);
        } catch (BadRequestException $badRequestException) {
            return $this->json([
                'code' => 400,
                'message' => $badRequestException->getMessage(),
            ], 400);
        } catch (NotFoundHttpException $notFoundHttpException) {
            return $this->json([
                'code' => 404,
                'message' => $notFoundHttpException->getMessage(),
            ], 404);
        } catch (UnauthorizedHttpException $unauthorizedHttpException) {
            return $this->json([
                'code' => 401,
                'message' => $unauthorizedHttpException->getMessage(),
            ], 401);
        }
    }

    /**
     * @Route("/checkups/history", name="doctor_checkups_history", methods={"GET"})
     */
    public function doctorCheckupsHistory(
        Request $request,
        UserRepository $userRepository,
        CheckupRepository $checkupRepository,
        JWTService $jwtService
    ): Response {
        try {
            $requestToken = $request->headers->get('Authorization');
            if (null === $requestToken) {
                throw new UnauthorizedHttpException('', 'You must provide Authorization header with Bearer token');
            }
            // check token
            $tokenParts = explode(' ', $requestToken);
            if (2 !== count($tokenParts) || 'Bearer' !== $tokenParts[0]) {
                throw new UnauthorizedHttpException('', 'You must provide Authorization header with Bearer token');
            }
            // validate token
            $jwtService->validateToken($tokenParts[1]);
            $token = $jwtService->parseToken($tokenParts[1]);

            // get doctor id
            $doctorId = (int) $token->claims()->get('user_id');
            // get doctor
            $doctor = $userRepository->find($doctorId);
            if (null === $doctor) {
                throw new NotFoundHttpException('Doctor not found');
            }
            $checkups = $checkupRepository->getDoctorCheckupsHistory($doctor);
            $checkupsDto = [];
            foreach ($checkups as $checkup) {
                $checkupsDto[] = [
                    'id' => $checkup->getId(),
                    'client' => $checkup->getPet()->getOwner()->getAccount()->getFullName(),
                    'pet' => $checkup->getPet()->getName(),
                    'date' => $checkup->getDate(),
                ];
            }
            return $this->json($checkupsDto);
        } catch (BadRequestException $badRequestException) {
            return $this->json([
                'code' => 400,
                'message' => $badRequestException->getMessage(),
            ], 400);
        } catch (NotFoundHttpException $notFoundHttpException) {
            return $this->json([
                'code' => 404,
                'message' => $notFoundHttpException->getMessage(),
            ], 404);
        } catch (UnauthorizedHttpException $unauthorizedHttpException) {
            return $this->json([
                'code' => 401,
                'message' => $unauthorizedHttpException->getMessage(),
            ], 401);
        }
    }
}
