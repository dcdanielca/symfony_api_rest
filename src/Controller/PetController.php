<?php

namespace App\Controller;

use App\Entity\Pet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PetController extends AbstractController
{
    /**
     * @Route("/pet", name="createPet", methods={"POST"})
     */
    public function createPet(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $type = $data['type'];
        $photoUrls = $data['photoUrls'];

        if (empty($name) || empty($type)) {
            throw new NotFoundHttpException('Expecting mandatory parameters');
        }

        $em = $this->getDoctrine()->getManager();
        $pet = new Pet();
        $pet->setName($name)
            ->setType($type)
            ->setPhotoUrls($photoUrls);
        $em->persist($pet);
        $em->flush();

        return new JsonResponse(['status' => 'Pet created'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/pets", name="getPets", methods={"GET"})
     */
    public function getAllPets(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $pets = $em->getRepository(Pet::class)->findAll();
        $data = [];
        foreach($pets as $pet){
            $data[] = [
                'id'=>$pet->getId(),
                'name'=>$pet->getName(),
                'type'=>$pet->getType(),
                'photoUrls'=>$pet->getPhotoUrls()
            ];
        }
        return new JsonResponse($data, Response::HTTP_CREATED);
    }

    /**
     * @Route("/pet/{id}", name="getPet", methods={"GET"})
     */
    public function getPet($id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $pet = $em->getRepository(Pet::class)->find($id);
        $data = [
            'id'=>$pet->getId(),
            'name'=>$pet->getName(),
            'type'=>$pet->getType(),
            'photoUrls'=>$pet->getPhotoUrls()
        ];
        return new JsonResponse($data, Response::HTTP_CREATED);
    }

    /**
     * @Route("/pet/{id}", name="updatePet", methods={"PUT"})
     */
    public function updatePet(Request $request, $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $pet = $em->getRepository(Pet::class)->find($id);
        $data = json_decode($request->getContent(), true);

        empty($data['name']) ? true : $pet->setName($data['name']);
        empty($data['type']) ? true : $pet->setType($data['type']);
        empty($data['photoUrls']) ? true : $pet->setPhotoUrls($data['photoUrls']);

        $em->persist($pet);
        $em->flush();

        return new JsonResponse(['status'=>'Pet updated'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/pet/{id}", name="removePet", methods={"DELETE"})
     */
    public function removePet(Request $request, $id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $pet = $em->getRepository(Pet::class)->find($id);

        $em->remove($pet);
        $em->flush();

        return new JsonResponse(['status'=>'Pet deleted'], Response::HTTP_CREATED);
    }
}
