<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Repositories\AutorRepository;
use App\Repositories\ProductRepository;
use App\Services\AutorService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AutorController
{
    private View $view;
    private AutorRepository $repo;
    private AutorService $service;

    private AutorRepository $autorRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new AutorRepository();
        $this->service = new AutorService();
        $this->autorRepo = new autorRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 5;
        $total = $this->repo->countAll();
        $autors = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);
        $html = $this->view->render('admin/autors/index', compact('autors', 'page', 'pages'));
        return new Response($html);
    }

    public function create(): Response
    {
        $html = $this->view->render('admin/autors/create', ['csrf' => Csrf::token(), 'errors' => []]);
        return new Response($html);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $errors = $this->service->validate($request->request->all());
        if ($errors) {
            $html = $this->view->render('admin/autors/create', ['csrf' => Csrf::token(), 'errors' => $errors, 'old' => $request->request->all()]);
            return new Response($html, 422);
        }
        $autor = $this->service->make($request->request->all());
        $id = $this->repo->create($autor);
        return new RedirectResponse('/admin/autors/show?id=' . $id);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $autor = $this->repo->findById($id);
        if (!$autor) return new Response('Autor não encontrado', 404);
        $html = $this->view->render('admin/autors/show', ['autor' => $autor]);
        return new Response($html);
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $autor = $this->repo->findById($id);
        if (!$autor) return new Response('Autor não encontrado', 404);
        $html = $this->view->render('admin/autors/edit', ['autor' => $autor, 'csrf' => Csrf::token(), 'errors' => []]);
        return new Response($html);
    }

    public function update(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $data = $request->request->all();
        $file = $request->files->get('image');
        $errors = $this->service->validate($data);
        if ($errors) {
            $html = $this->view->render('admin/autors/edit', ['autor' => array_merge($this->repo->find((int)$data['id']), $data), 'csrf' => Csrf::token(), 'errors' => $errors]);
            return new Response($html, 422);
        }
        $autor = $this->service->make($data);
        if (!$autor->id) return new Response('ID inválido', 422);
        $this->repo->update($autor);
        return new RedirectResponse('/admin/autors/show?id=' . $autor->id);
    }

    public function delete(Request $request): Response
    {
        // Pegar produto com Autor
        $autors = $this->autorRepo->findById((int)$request->request->get('id', 0));
        if ($autor) {
            Flash::push("danger", "Autor não pode ser excluído");
            return new RedirectResponse('/admin/autors');
        }


        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $id = (int)$request->request->get('id', 0);
        if ($id > 0) $this->repo->delete($id);
        return new RedirectResponse('/admin/autors');
    }
}
