<div id="media-picker-modal" class="fixed inset-0 z-50 hidden flex-col bg-white">
    <div class="flex items-center justify-between border-b border-[#e6d9c5] px-6 py-4">
        <h2 id="media-picker-title" class="text-lg font-semibold text-[#523923]">Choose an image</h2>
        <button type="button" onclick="closeMediaPicker()" class="text-2xl leading-none text-[#8a6c56] hover:text-[#7c2d12]" aria-label="Close">&times;</button>
    </div>

    <div class="flex gap-1 border-b border-[#e6d9c5] px-6" id="media-picker-tabs">
        <button type="button" data-tab="library" onclick="setMediaPickerTab('library')" class="media-picker-tab-btn border-b-2 border-[#7c2d12] px-4 py-3 text-sm font-medium text-[#7c2d12]">Library</button>
        <button type="button" data-tab="upload" onclick="setMediaPickerTab('upload')" class="media-picker-tab-btn border-b-2 border-transparent px-4 py-3 text-sm font-medium text-[#8a6c56] hover:text-[#5a4538]">Upload Files</button>
    </div>

    <div class="flex flex-1 overflow-hidden">
        <div class="flex flex-1 flex-col overflow-hidden">
            <div id="media-picker-panel-library" class="flex flex-1 flex-col overflow-hidden">
                <div class="border-b border-[#e6d9c5] px-6 py-4">
                    <input type="text" id="media-picker-search" placeholder="Search by title, alt text or file name…" autocomplete="off" oninput="filterMediaPicker(this.value)" class="w-full max-w-sm rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-2.5 text-sm outline-none focus:border-[#7c2d12]" />
                </div>
                <div class="flex-1 overflow-y-auto p-6">
                    <div id="media-picker-empty" class="text-center text-sm text-[#8a6c56] {{ $mediaLibrary->isEmpty() ? '' : 'hidden' }}">No media uploaded yet. Upload one from the "Upload Files" tab.</div>
                    <div id="media-picker-grid" class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-7">
                        @foreach ($mediaLibrary as $item)
                            <div
                                class="media-picker-tile relative aspect-square cursor-pointer overflow-hidden rounded-xl border-2 border-transparent"
                                data-id="{{ $item->id }}"
                                data-url="{{ $item->url() }}"
                                data-thumb="{{ $item->thumbnailUrl() }}"
                                data-title="{{ $item->title }}"
                                data-alt-text="{{ $item->alt_text }}"
                                data-file-name="{{ $item->file_name }}"
                                data-dimensions="{{ $item->dimensions ?? '—' }}"
                                data-size="{{ $item->formatted_size }}"
                                data-uploaded-by="{{ $item->uploader->name ?? '—' }}"
                                data-destroy-url="{{ route('media.destroy', $item) }}"
                            >
                                <img src="{{ $item->thumbnailUrl() }}" alt="{{ $item->alt_text }}" class="h-full w-full object-cover" />
                                <input type="checkbox" class="media-picker-checkbox absolute right-1.5 top-1.5 h-4 w-4 rounded border-[#d9c9ae]" onclick="event.stopPropagation()" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="media-picker-panel-upload" class="hidden flex-1 flex-col overflow-y-auto p-6">
                <div class="flex flex-wrap items-center gap-3">
                    <input type="file" id="media-picker-upload-input" accept="image/*" multiple class="flex-1 rounded-2xl border border-[#d9c9ae] bg-[#faf4ec] px-4 py-3 text-sm outline-none file:mr-3 file:rounded-full file:border-0 file:bg-[#7C2D12] file:px-4 file:py-2 file:text-white" />
                    <button type="button" id="media-picker-upload-btn" onclick="uploadMediaPickerFiles()" class="rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610]">Upload</button>
                </div>
                <p id="media-picker-upload-status" class="mt-2 text-xs text-[#8a6c56]"></p>
                <p class="mt-1 text-xs text-[#8a6c56]">Uploaded files switch you to the Library tab, already selected.</p>
            </div>
        </div>

        <div class="hidden w-full max-w-xs shrink-0 overflow-y-auto border-l border-[#e6d9c5] bg-[#fffaf5] p-6 sm:flex sm:flex-col">
            <div id="media-picker-detail-empty" class="text-sm text-[#8a6c56]">Check an image to see its details here.</div>
            <div id="media-picker-detail-content" class="hidden space-y-4">
                <img id="media-picker-detail-preview" src="" alt="" class="max-h-40 w-full rounded-xl object-contain bg-[#faf4ec]" />
                <dl class="space-y-1 text-sm text-[#5a4538]">
                    <div><span class="font-medium">Dimensions:</span> <span id="media-picker-detail-dimensions"></span></div>
                    <div><span class="font-medium">Size:</span> <span id="media-picker-detail-size"></span></div>
                </dl>
                <button type="button" id="media-picker-detail-delete" class="text-sm text-[#7c2d12] underline hover:text-[#5a1d0c]">Permanently delete</button>
                <div>
                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">URL</label>
                    <input type="text" id="media-picker-detail-url" readonly class="w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2 text-xs outline-none" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Uploaded by</label>
                    <input type="text" id="media-picker-detail-uploaded-by" readonly class="w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2 text-sm outline-none" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">File Name</label>
                    <input type="text" id="media-picker-detail-file-name" readonly class="w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2 text-sm outline-none" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Title</label>
                    <input type="text" id="media-picker-detail-title" readonly class="w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2 text-sm outline-none" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-[#8a6c56]">Alt Text</label>
                    <input type="text" id="media-picker-detail-alt-text" readonly class="w-full rounded-xl border border-[#d9c9ae] bg-white px-3 py-2 text-sm outline-none" />
                </div>
            </div>

            <button type="button" id="media-picker-upload-confirm-btn" onclick="confirmMediaPicker()" disabled class="mt-6 w-full rounded-full bg-[#7C2D12] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#6b2610] disabled:cursor-not-allowed disabled:opacity-40">Upload</button>
        </div>
    </div>
</div>

<script>
    const mediaPickerCsrfToken = "{{ csrf_token() }}";
    const mediaPickerUploadUrl = "{{ route('media.store') }}";

    let mediaPickerMode = 'single';
    let mediaPickerOnConfirm = null;

    function openMediaPicker(mode, onConfirm) {
        mediaPickerMode = mode;
        mediaPickerOnConfirm = onConfirm;

        document.querySelectorAll('.media-picker-tile').forEach((tile) => {
            tile.classList.remove('border-[#7c2d12]');
            tile.querySelector('.media-picker-checkbox').checked = false;
        });
        showMediaPickerDetail(null);
        updateMediaPickerConfirmState();
        document.getElementById('media-picker-title').textContent = mode === 'multiple' ? 'Choose images for the gallery' : 'Choose an image';

        setMediaPickerTab('library');
        document.getElementById('media-picker-upload-status').textContent = '';

        const search = document.getElementById('media-picker-search');
        search.value = '';
        filterMediaPicker('');

        const modal = document.getElementById('media-picker-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeMediaPicker() {
        const modal = document.getElementById('media-picker-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function setMediaPickerTab(tab) {
        document.querySelectorAll('.media-picker-tab-btn').forEach((btn) => {
            const active = btn.dataset.tab === tab;
            btn.classList.toggle('border-[#7c2d12]', active);
            btn.classList.toggle('text-[#7c2d12]', active);
            btn.classList.toggle('border-transparent', !active);
            btn.classList.toggle('text-[#8a6c56]', !active);
        });
        document.getElementById('media-picker-panel-library').classList.toggle('hidden', tab !== 'library');
        document.getElementById('media-picker-panel-upload').classList.toggle('hidden', tab !== 'upload');
    }

    function filterMediaPicker(term) {
        term = term.toLowerCase();
        document.querySelectorAll('.media-picker-tile').forEach((tile) => {
            const haystack = `${tile.dataset.title} ${tile.dataset.altText} ${tile.dataset.fileName}`.toLowerCase();
            tile.classList.toggle('hidden', Boolean(term) && !haystack.includes(term));
        });
    }

    function tileToItem(tile) {
        return {
            id: tile.dataset.id,
            url: tile.dataset.url,
            thumb: tile.dataset.thumb,
            title: tile.dataset.title,
        };
    }

    function checkedTiles() {
        return Array.from(document.querySelectorAll('.media-picker-tile')).filter((tile) => tile.querySelector('.media-picker-checkbox').checked);
    }

    function updateMediaPickerConfirmState() {
        const count = checkedTiles().length;
        const btn = document.getElementById('media-picker-upload-confirm-btn');
        btn.disabled = count === 0;
        btn.textContent = count > 1 ? `Upload (${count})` : 'Upload';
    }

    function showMediaPickerDetail(tile) {
        const empty = document.getElementById('media-picker-detail-empty');
        const content = document.getElementById('media-picker-detail-content');

        if (!tile) {
            empty.classList.remove('hidden');
            content.classList.add('hidden');
            return;
        }

        empty.classList.add('hidden');
        content.classList.remove('hidden');
        document.getElementById('media-picker-detail-preview').src = tile.dataset.thumb;
        document.getElementById('media-picker-detail-dimensions').textContent = tile.dataset.dimensions;
        document.getElementById('media-picker-detail-size').textContent = tile.dataset.size;
        document.getElementById('media-picker-detail-url').value = tile.dataset.url;
        document.getElementById('media-picker-detail-uploaded-by').value = tile.dataset.uploadedBy;
        document.getElementById('media-picker-detail-file-name').value = tile.dataset.fileName;
        document.getElementById('media-picker-detail-title').value = tile.dataset.title;
        document.getElementById('media-picker-detail-alt-text').value = tile.dataset.altText;

        const deleteBtn = document.getElementById('media-picker-detail-delete');
        deleteBtn.onclick = () => deleteMediaPickerTile(tile);
    }

    function deleteMediaPickerTile(tile) {
        if (!window.confirm(`Permanently delete "${tile.dataset.title}"? This cannot be undone.`)) return;

        fetch(tile.dataset.destroyUrl, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': mediaPickerCsrfToken,
            },
        }).finally(() => {
            tile.remove();
            showMediaPickerDetail(null);
            updateMediaPickerConfirmState();
        });
    }

    function toggleTileChecked(tile, checked) {
        const checkbox = tile.querySelector('.media-picker-checkbox');

        if (mediaPickerMode === 'single' && checked) {
            document.querySelectorAll('.media-picker-tile').forEach((other) => {
                if (other !== tile) {
                    other.querySelector('.media-picker-checkbox').checked = false;
                    other.classList.remove('border-[#7c2d12]');
                }
            });
        }

        checkbox.checked = checked;
        tile.classList.toggle('border-[#7c2d12]', checked);
        if (checked) showMediaPickerDetail(tile);
        updateMediaPickerConfirmState();
    }

    function bindMediaPickerTile(tile) {
        tile.addEventListener('click', () => toggleTileChecked(tile, !tile.querySelector('.media-picker-checkbox').checked));
        tile.querySelector('.media-picker-checkbox').addEventListener('change', (event) => toggleTileChecked(tile, event.target.checked));
    }

    document.querySelectorAll('.media-picker-tile').forEach(bindMediaPickerTile);

    function confirmMediaPicker() {
        const items = checkedTiles().map(tileToItem);
        if (!items.length) return;
        if (mediaPickerOnConfirm) mediaPickerOnConfirm(items);
        closeMediaPicker();
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMediaPicker();
    });

    function addTileToGrid(item) {
        const grid = document.getElementById('media-picker-grid');
        const tile = document.createElement('div');
        tile.className = 'media-picker-tile relative aspect-square cursor-pointer overflow-hidden rounded-xl border-2 border-transparent';
        tile.dataset.id = item.id;
        tile.dataset.url = item.url;
        tile.dataset.thumb = item.thumb;
        tile.dataset.title = item.title;
        tile.dataset.altText = item.alt_text;
        tile.dataset.fileName = item.file_name;
        tile.dataset.dimensions = item.dimensions || '—';
        tile.dataset.size = item.size || '—';
        tile.dataset.uploadedBy = item.uploaded_by || '—';
        tile.dataset.destroyUrl = item.destroy_url || '';
        tile.innerHTML = `
            <img src="${item.thumb}" alt="${item.title}" class="h-full w-full object-cover" />
            <input type="checkbox" class="media-picker-checkbox absolute right-1.5 top-1.5 h-4 w-4 rounded border-[#d9c9ae]" onclick="event.stopPropagation()" />
        `;
        bindMediaPickerTile(tile);
        grid.prepend(tile);
        document.getElementById('media-picker-empty').classList.add('hidden');
        return tile;
    }

    function uploadMediaPickerFiles() {
        const input = document.getElementById('media-picker-upload-input');
        const status = document.getElementById('media-picker-upload-status');
        const uploadBtn = document.getElementById('media-picker-upload-btn');

        if (!input.files.length) {
            status.textContent = 'Choose a file first.';
            return;
        }

        const formData = new FormData();
        Array.from(input.files).forEach((file) => formData.append('files[]', file));

        uploadBtn.disabled = true;
        status.textContent = 'Uploading…';

        fetch(mediaPickerUploadUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': mediaPickerCsrfToken,
            },
            body: formData,
        })
            .then((response) => {
                if (!response.ok) throw new Error('Upload failed');
                return response.json();
            })
            .then((data) => {
                const newTiles = data.media.map((item) => addTileToGrid(item));
                newTiles.forEach((tile) => toggleTileChecked(tile, true));
                input.value = '';
                status.textContent = '';
                setMediaPickerTab('library');
            })
            .catch(() => {
                status.textContent = 'Upload failed. Please try again.';
            })
            .finally(() => {
                uploadBtn.disabled = false;
            });
    }

    function setSingleImage(hiddenInputId, previewImgId, item) {
        if (!item) return;
        document.getElementById(hiddenInputId).value = item.id;
        const preview = document.getElementById(previewImgId);
        preview.src = item.thumb;
        preview.classList.remove('hidden');
    }

    function addGalleryLibraryImages(items) {
        const container = document.getElementById('gallery-library-selected');
        items.forEach((item) => {
            if (container.querySelector(`input[value="${item.id}"]`)) return;

            const wrapper = document.createElement('span');
            wrapper.className = 'relative inline-block';
            wrapper.innerHTML = `
                <img src="${item.thumb}" alt="${item.title}" class="h-12 w-12 rounded-lg object-cover" />
                <button type="button" class="absolute -right-1.5 -top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#e46a5c] text-[10px] text-white hover:bg-[#d65847]" aria-label="Remove">&times;</button>
                <input type="hidden" name="gallery_media_ids[]" value="${item.id}" />
            `;
            wrapper.querySelector('button').addEventListener('click', () => wrapper.remove());
            container.appendChild(wrapper);
        });
    }

    function addColorLibraryImages(colorId, items) {
        const container = document.getElementById('color-images-' + colorId);
        items.forEach((item) => {
            if (container.querySelector(`input[value="${item.id}"]`)) return;

            const wrapper = document.createElement('span');
            wrapper.className = 'relative inline-block';
            wrapper.innerHTML = `
                <img src="${item.thumb}" alt="${item.title}" class="h-12 w-12 rounded-lg object-cover" />
                <button type="button" class="absolute -right-1.5 -top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#e46a5c] text-[10px] text-white hover:bg-[#d65847]" aria-label="Remove">&times;</button>
                <input type="hidden" name="color_media_ids[${colorId}][]" value="${item.id}" />
            `;
            wrapper.querySelector('button').addEventListener('click', () => wrapper.remove());
            container.appendChild(wrapper);
        });
    }
</script>
