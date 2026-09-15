<?php

namespace App\Http\Controllers;

use App\Enums\StudentStatus;
use App\Models\Achievement;
use App\Models\AlumniProfile;
use App\Models\Facility;
use App\Models\GalleryAlbum;
use App\Models\Post;
use App\Models\PPDBWave;
use App\Models\SchoolEvent;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

// Public school website: home, profile, news, agenda, achievements, gallery,
// teacher directory and contact. The school is resolved by ResolveTenant
// (subdomain, ?tenant= or the default school). Only published content is shown.
class WebsiteController extends Controller
{
    private function tenant(): Tenant
    {
        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        return $tenant;
    }

    public function home(): View
    {
        $tenant = $this->tenant();

        return view('website.home', [
            'tenant' => $tenant,
            'stats' => [
                'students' => Student::where('status', StudentStatus::ACTIVE)->count(),
                'teachers' => Teacher::count(),
                'achievements' => Achievement::published()->count(),
                'alumni' => AlumniProfile::count(),
            ],
            'featured' => Post::published()->orderByDesc('is_featured')->latest('published_at')->limit(3)->get(),
            'events' => SchoolEvent::published()->upcoming()->orderBy('starts_at')->limit(4)->get(),
            'achievements' => Achievement::published()->latest('achieved_at')->limit(4)->get(),
            'albums' => GalleryAlbum::published()->withCount('items')->latest('event_date')->limit(6)->get(),
            'teachers' => Teacher::orderByDesc('is_homeroom_teacher')->orderBy('full_name')->limit(8)->get(),
            'openWave' => PPDBWave::where('is_active', true)->where('opens_at', '<=', now())->where('closes_at', '>=', now())->orderBy('closes_at')->first(),
        ]);
    }

    public function about(): View
    {
        return view('website.about', [
            'tenant' => $this->tenant(),
            'facilities' => Facility::orderBy('name')->get(),
            'stats' => [
                'students' => Student::where('status', StudentStatus::ACTIVE)->count(),
                'teachers' => Teacher::count(),
                'facilities' => Facility::count(),
            ],
        ]);
    }

    public function news(Request $request): View
    {
        $category = in_array($request->query('category'), Post::CATEGORIES, true) ? $request->query('category') : null;

        return view('website.news', [
            'tenant' => $this->tenant(),
            'category' => $category,
            'posts' => Post::published()
                ->when($category, fn ($q) => $q->where('category', $category))
                ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%' . str_replace(['%', '_'], ['\%', '\_'], (string) $request->query('q')) . '%'))
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString(),
        ]);
    }

    public function newsShow(string $slug): View
    {
        $post = Post::published()->where('slug', $slug)->with('author')->firstOrFail();
        $post->increment('views');

        return view('website.news-show', [
            'tenant' => $this->tenant(),
            'post' => $post,
            'related' => Post::published()->whereKeyNot($post->id)->where('category', $post->category)->latest('published_at')->limit(3)->get(),
        ]);
    }

    public function agenda(): View
    {
        return view('website.agenda', [
            'tenant' => $this->tenant(),
            'upcoming' => SchoolEvent::published()->upcoming()->orderBy('starts_at')->get(),
            'past' => SchoolEvent::published()->where('starts_at', '<', now()->startOfDay())->latest('starts_at')->limit(12)->get(),
        ]);
    }

    public function achievements(Request $request): View
    {
        $level = array_key_exists((string) $request->query('level'), Achievement::levelLabels()) ? $request->query('level') : null;

        return view('website.achievements', [
            'tenant' => $this->tenant(),
            'level' => $level,
            'achievements' => Achievement::published()
                ->when($level, fn ($q) => $q->where('level', $level))
                ->latest('achieved_at')
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function gallery(): View
    {
        return view('website.gallery', [
            'tenant' => $this->tenant(),
            'albums' => GalleryAlbum::published()->withCount('items')->latest('event_date')->paginate(12),
        ]);
    }

    public function album(string $slug): View
    {
        return view('website.album', [
            'tenant' => $this->tenant(),
            'album' => GalleryAlbum::published()->where('slug', $slug)->with('items')->firstOrFail(),
        ]);
    }

    public function teachers(): View
    {
        return view('website.teachers', [
            'tenant' => $this->tenant(),
            'teachers' => Teacher::with('homeroomClassroom')->orderByDesc('is_homeroom_teacher')->orderBy('full_name')->get(),
        ]);
    }

    public function contact(): View
    {
        return view('website.contact', ['tenant' => $this->tenant()]);
    }

    // XML sitemap of every public page, for search engines.
    public function sitemap(): Response
    {
        $tenant = $this->tenant();
        $query = request()->has('tenant') ? ['tenant' => $tenant->slug] : [];

        $urls = collect(['website.home', 'website.about', 'website.news', 'website.agenda', 'website.achievements', 'website.gallery', 'website.teachers', 'website.contact', 'alumni.index', 'ppdb.index'])
            ->map(fn (string $name) => ['loc' => route($name, $query), 'lastmod' => now()->toAtomString()])
            ->merge(Post::published()->latest('published_at')->limit(500)->get()->map(fn (Post $post) => [
                'loc' => route('website.news.show', ['slug' => $post->slug] + $query),
                'lastmod' => $post->updated_at->toAtomString(),
            ]))
            ->merge(GalleryAlbum::published()->get()->map(fn (GalleryAlbum $album) => [
                'loc' => route('website.gallery.show', ['slug' => $album->slug] + $query),
                'lastmod' => $album->updated_at->toAtomString(),
            ]));

        return response()->view('website.sitemap', ['urls' => $urls], 200, ['Content-Type' => 'application/xml']);
    }
}
