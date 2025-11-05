<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Repositories\ProducerRepository;
use App\Repositories\ProductRepository;
use App\Services\ProducerService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProducerController
{
    private View $view;
    private ProducerRepository $repo;
    private ProducerService $service;

    private ProducerRepository $producerRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new ProducerRepository();
        $this->service = new ProducerService();
        $this->producerRepo = new producerRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 5;
        $total = $this->repo->countAll();
        $producers = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);
        $html = $this->view->render('admin/producers/index', compact('producers', 'page', 'pages'));
        return new Response($html);
    }

    public function create(): Response
    {
        $html = $this->view->render('admin/producers/create', ['csrf' => Csrf::token(), 'errors' => []]);
        return new Response($html);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $errors = $this->service->validate($request->request->all());
        if ($errors) {
            $html = $this->view->render('admin/producers/create', ['csrf' => Csrf::token(), 'errors' => $errors, 'old' => $request->request->all()]);
            return new Response($html, 422);
        }
        $producer = $this->service->make($request->request->all());
        $id = $this->repo->create($producer);
        return new RedirectResponse('/admin/producers/show?id=' . $id);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $producer = $this->repo->findById($id);
        if (!$producer) return new Response('Produtora não encontrada', 404);
        $html = $this->view->render('admin/producers/show', ['producer' => $producer]);
        return new Response($html);
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $producer = $this->repo->findById($id);
        if (!$producer) return new Response('Produtora não encontrada', 404);
        $html = $this->view->render('admin/producers/edit', ['producer' => $producer, 'csrf' => Csrf::token(), 'errors' => []]);
        return new Response($html);
    }

    public function update(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $data = $request->request->all();
        $file = $request->files->get('image');
        $errors = $this->service->validate($data);
        if ($errors) {
            $html = $this->view->render('admin/producers/edit', ['producer' => array_merge($this->repo->find((int)$data['id']), $data), 'csrf' => Csrf::token(), 'errors' => $errors]);
            return new Response($html, 422);
        }
        $producer = $this->service->make($data);
        if (!$producer->id) return new Response('ID inválido', 422);
        $this->repo->update($producer);
        return new RedirectResponse('/admin/producers/show?id=' . $producer->id);
    }

    public function delete(Request $request): Response
    {
        // Pegar produto com producer
        $producers = $this->producerRepo->findById((int)$request->request->get('id', 0));
        if ($producer) {
            Flash::push("danger", "Produtora não pode ser excluída");
            return new RedirectResponse('/admin/producers');
        }


        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $id = (int)$request->request->get('id', 0);
        if ($id > 0) $this->repo->delete($id);
        return new RedirectResponse('/admin/producers');
    }
}
