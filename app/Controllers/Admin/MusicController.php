<?php

namespace App\Controllers\Admin;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\View;
use App\Repositories\MusicRepository;
use App\Repositories\ProductRepository;
use App\Services\MusicService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MusicController
{
    private View $view;
    private MusicRepository $repo;
    private MusicService $service;
    private AutorRepository $categoryRepo;
    private MusicRepository $musicRepo;

    public function __construct()
    {
        $this->view = new View();
        $this->repo = new MusicRepository();
        $this->service = new MusicService();
        $this->musicRepo = new MusicRepository();
    }

    public function index(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 5;
        $total = $this->repo->countAll();
        $musics = $this->repo->paginate($page, $perPage);
        $pages = (int)ceil($total / $perPage);
        $html = $this->view->render('admin/musics/index', compact('musics', 'page', 'pages'));
        return new Response($html);
    }

    public function create(): Response
    {
        $html = $this->view->render('admin/musics/create', ['csrf' => Csrf::token(), 'errors' => []]);
        return new Response($html);
    }

    public function store(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $errors = $this->service->validate($request->request->all());
        if ($errors) {
            $html = $this->view->render('admin/musics/create', ['csrf' => Csrf::token(), 'errors' => $errors, 'old' => $request->request->all()]);
            return new Response($html, 422);
        }
        $music = $this->service->make($request->request->all());
        $id = $this->repo->create($music);
        return new RedirectResponse('/admin/musics/show?id=' . $id);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $music = $this->repo->findById($id);
        if (!$music) return new Response('Música não encontrada', 404);
        $html = $this->view->render('admin/musics/show', ['music' => $music]);
        return new Response($html);
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->query->get('id', 0);
        $music = $this->repo->findById($id);
        if (!$music) return new Response('Música não encontrada', 404);
        $html = $this->view->render('admin/musics/edit', ['music' => $music, 'csrf' => Csrf::token(), 'errors' => []]);
        return new Response($html);
    }

    public function update(Request $request): Response
    {
        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $data = $request->request->all();
        $file = $request->files->get('image');
        $errors = $this->service->validate($data);
        if ($errors) {
            $html = $this->view->render('admin/musics/edit', ['music' => array_merge($this->repo->find((int)$data['id']), $data), 'csrf' => Csrf::token(), 'errors' => $errors]);
            return new Response($html, 422);
        }
        $music = $this->service->make($data);
        if (!$music->id) return new Response('ID inválido', 422);
        $this->repo->update($music);
        return new RedirectResponse('/admin/musics/show?id=' . $music->id);
    }

    public function delete(Request $request): Response
    {
        // Pegar produto com music
        $musics = $this->musicRepo->findById((int)$request->request->get('id', 0));
        if ($music) {
            Flash::push("danger", "Música não pode ser excluída");
            return new RedirectResponse('/admin/musics');
        }


        if (!Csrf::validate($request->request->get('_csrf'))) return new Response('Token CSRF inválido', 419);
        $id = (int)$request->request->get('id', 0);
        if ($id > 0) $this->repo->delete($id);
        return new RedirectResponse('/admin/autors');
    }
}
