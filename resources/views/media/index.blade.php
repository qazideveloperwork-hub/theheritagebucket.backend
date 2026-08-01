@extends('layout')
@section('title', 'Media Library')
@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Media Library'],
    ]])

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-[#523923]">Media Library</h1>
        <button type="button" id="toggle-upload-panel" class="rounded-full bg-[#7C2D12] px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">+ New file</button>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-[#c7d9c0] bg-[#eef6ea] px-4 py-3 text-sm text-[#3f6a2c]">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-[#e3b3a3] bg-[#fdecea] px-4 py-3 text-sm text-[#7c2d12]">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section id="upload-panel" class="mb-6 hidden rounded-[28px] border border-[#e6d9c5] bg-white p-6">
        <h2 class="text-lg font-semibold text-[#523923]">Upload files</h2>
        <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-center gap-3">
            @csrf
            <input type="file" name="files[]" accept="image/*" multiple required class="flex-1 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none file:mr-3 file:rounded-full file:border-0 file:bg-[#7C2D12] file:px-4 file:py-2 file:text-white" />
            <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Upload</button>
        </form>
        <p class="mt-2 text-xs text-[#8a6c56]">JPG, PNG, WEBP or GIF, up to 8&nbsp;MB each. Thumbnail, medium and large sizes are generated automatically.</p>
    </section>

    <form action="{{ route('media.index') }}" method="GET" id="media-search-form" class="mb-6">
        <input type="text" name="q" value="{{ request('q') }}" id="media-search-input" placeholder="Search by title, alt text or file name…" autocomplete="off" class="w-full max-w-sm rounded-2xl border border-[#d9c9ae] bg-white px-4 py-3 text-sm outline-none focus:border-[#7c2d12]" />
    </form>

    @if ($media->isEmpty())
        <div class="rounded-[28px] border border-[#e6d9c5] bg-white p-10 text-center text-sm text-[#8a6c56]">No media uploaded yet.</div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            @foreach ($media as $item)
                <button
                    type="button"
                    class="media-tile group overflow-hidden rounded-2xl border border-[#e6d9c5] bg-white"
                    data-id="{{ $item->id }}"
                    data-url="{{ $item->url() }}"
                    data-preview="{{ $item->largeUrl() }}"
                    data-file-name="{{ $item->file_name }}"
                    data-original-name="{{ $item->original_name }}"
                    data-mime-type="{{ $item->mime_type }}"
                    data-uploaded-on="{{ $item->created_at->format('F j, Y') }}"
                    data-size="{{ $item->formatted_size }}"
                    data-dimensions="{{ $item->dimensions ?? '—' }}"
                    data-title="{{ $item->title }}"
                    data-alt-text="{{ $item->alt_text }}"
                    data-uploaded-by="{{ $item->uploader->name ?? '—' }}"
                    data-update-url="{{ route('media.update', $item) }}"
                    data-destroy-url="{{ route('media.destroy', $item) }}"
                >
                    <span class="block aspect-square w-full overflow-hidden bg-[#faf4ec]">
                        <img src="{{ $item->thumbnailUrl() }}" alt="{{ $item->alt_text }}" class="h-full w-full object-cover transition group-hover:scale-105" />
                    </span>
                </button>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $media->links() }}
        </div>
    @endif

    <div id="media-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="flex max-h-[90vh] w-full max-w-4xl overflow-hidden rounded-[28px] bg-white">
            <div class="hidden flex-1 items-center justify-center bg-[#faf4ec] p-6 sm:flex">
                <img id="media-modal-preview" src="" alt="" class="max-h-[70vh] max-w-full rounded-xl object-contain" />
            </div>
            <div class="w-full max-w-sm shrink-0 overflow-y-auto p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#523923]">Image Details</h2>
                    <button type="button" id="media-modal-close" class="text-2xl leading-none text-[#8a6c56] hover:text-[#7c2d12]" aria-label="Close">&times;</button>
                </div>

                <dl class="space-y-1 text-sm text-[#5a4538]">
                    <div><span class="font-medium">File Name:</span> <span id="media-modal-file-name"></span></div>
                    <div><span class="font-medium">File Type:</span> <span id="media-modal-mime-type"></span></div>
                    <div><span class="font-medium">Uploaded On:</span> <span id="media-modal-uploaded-on"></span></div>
                    <div><span class="font-medium">File Size:</span> <span id="media-modal-size"></span></div>
                    <div><span class="font-medium">Dimensions:</span> <span id="media-modal-dimensions"></span></div>
                </dl>

                <form id="media-delete-form" method="POST" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-[#7c2d12] underline hover:text-[#5a1d0c]">permanently delete</button>
                </form>

                <form id="media-edit-form" method="POST" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">URL</label>
                        <input type="text" id="media-modal-url" readonly class="w-full rounded-xl border border-[#d9c9ae] bg-[#faf4ec] px-3 py-2 text-xs outline-none" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Uploaded by</label>
                        <input type="text" id="media-modal-uploaded-by" readonly class="w-full rounded-xl border border-[#d9c9ae] bg-[#faf4ec] px-3 py-2 text-sm outline-none" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Title</label>
                        <input type="text" name="title" id="media-modal-title" class="w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2 text-sm outline-none focus:border-[#7c2d12]" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Alt Text</label>
                        <input type="text" name="alt_text" id="media-modal-alt-text" class="w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2 text-sm outline-none focus:border-[#7c2d12]" />
                    </div>
                    <button type="submit" class="rounded-full bg-[#7C2D12] px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Save changes</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const searchInput = document.getElementById('media-search-input');
            const searchForm = document.getElementById('media-search-form');
            let searchTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => searchForm.submit(), 400);
            });

            const toggleUploadBtn = document.getElementById('toggle-upload-panel');
            const uploadPanel = document.getElementById('upload-panel');
            toggleUploadBtn.addEventListener('click', () => uploadPanel.classList.toggle('hidden'));

            const modal = document.getElementById('media-modal');
            const modalPreview = document.getElementById('media-modal-preview');
            const editForm = document.getElementById('media-edit-form');
            const deleteForm = document.getElementById('media-delete-form');

            function openModal(tile) {
                modalPreview.src = tile.dataset.preview;
                modalPreview.alt = tile.dataset.altText;
                document.getElementById('media-modal-file-name').textContent = tile.dataset.fileName;
                document.getElementById('media-modal-mime-type').textContent = tile.dataset.mimeType;
                document.getElementById('media-modal-uploaded-on').textContent = tile.dataset.uploadedOn;
                document.getElementById('media-modal-size').textContent = tile.dataset.size;
                document.getElementById('media-modal-dimensions').textContent = tile.dataset.dimensions;
                document.getElementById('media-modal-url').value = tile.dataset.url;
                document.getElementById('media-modal-uploaded-by').value = tile.dataset.uploadedBy;
                document.getElementById('media-modal-title').value = tile.dataset.title;
                document.getElementById('media-modal-alt-text').value = tile.dataset.altText;

                editForm.action = tile.dataset.updateUrl;
                deleteForm.action = tile.dataset.destroyUrl;
                deleteForm.onsubmit = () => window.confirm(`Permanently delete "${tile.dataset.originalName}"? This cannot be undone.`);

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.querySelectorAll('.media-tile').forEach((tile) => {
                tile.addEventListener('click', () => openModal(tile));
            });

            document.getElementById('media-modal-close').addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeModal();
            });
        })();
    </script>
@endsection
