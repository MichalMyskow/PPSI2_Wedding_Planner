<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Task;
use App\Service\TaskService;
use App\Entity\Wedding;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class TaskController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/task/update/{id}', name: 'task_update', methods: "PUT")]
    public function update(Request $request,ValidatorInterface $validator, $id)
    {
        $taskService  = new TaskService($this->getDoctrine()->getManager());
        $task = $taskService->getOne($id);
        $this->denyAccessUnlessGranted('edit', $task);

        $data = $request->toArray();
        $name = $data['name'];

        $task->setName($name);
            $errors = $validator->validate($task);
            if (count($errors)  === 0) {
            $taskService->saveTask($task);
            $responseData = ['status' => 'success'];
        
            } else{
                $responseData = ['status' => 'denied'];

            }

        return new Response(json_encode($responseData));
    }

    #[Route('/task/delete/{id}', name: 'task_delete', methods: "DELETE")]
    public function delete($id)
    {
        $taskService  = new TaskService($this->getDoctrine()->getManager());
        $task = $taskService->getOne($id);
        $this->denyAccessUnlessGranted('edit', $task);
        $taskService->deleteTask($task);
        $data = ['message' => 'deleted'];
        
        return new Response(json_encode($data));
        return $this->redirectToRoute('checklist');
    }

    #[Route('/task/show/{id}', name: 'task_show', methods: "GET")]
    public function show(Request $request, $id)
    {
        $taskService  = new TaskService($this->getDoctrine()->getManager());
        $task = $taskService->getOne($id);
        $this->denyAccessUnlessGranted('view', $task,);

        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);

        $jsonContent = $serializer->serialize($task, 'json');

        return new Response($jsonContent);
    }
    
    #[Route('/task/complete/{id}', name: 'task_complete', methods: "PUT")]
    public function complete(Request $request, $id)
    {
        $taskService  = new TaskService($this->getDoctrine()->getManager());
        $task = $taskService->getOne($id);
        $this->denyAccessUnlessGranted('edit', $task);
        $taskService->changeState($task, true);
        $data = ['message' => 'completed'];
        
        return new Response(json_encode($data));
    }

    #[Route('/task/cancel/{id}', name: 'task_cancel', methods: "PUT")]
    public function cancel(Request $request, $id)
    {
        $taskService  = new TaskService($this->getDoctrine()->getManager());
        $task = $taskService->getOne($id);
        $this->denyAccessUnlessGranted('edit', $task);
        $taskService->changeState($task, false);
        $data = ['message' => 'canceled'];
        
        return new Response(json_encode($data));
    }
}
