<div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-sm rounded-[28px] border border-[#e6d9c5] bg-white p-6 shadow-xl">
        <p id="confirm-modal-message" class="text-sm text-[#1b1b18]"></p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="closeConfirmModal()" class="rounded-full border border-[#d9c9ae] px-5 py-2.5 text-sm text-[#5a4538] hover:bg-[#f1e5d5]">Cancel</button>
            <button type="button" onclick="confirmModalProceed()" class="rounded-full bg-[#7C2D12] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#6b2610]">Confirm</button>
        </div>
    </div>
</div>
<script>
    let confirmModalForm = null;

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (form.dataset && form.dataset.confirm && !form.dataset.confirmed) {
            event.preventDefault();
            openConfirmModalForForm(form, form.dataset.confirm);
        }
    });

    function openConfirmModalForForm(form, message) {
        confirmModalForm = form;
        document.getElementById('confirm-modal-message').textContent = message;
        const modal = document.getElementById('confirm-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeConfirmModal() {
        confirmModalForm = null;
        const modal = document.getElementById('confirm-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function confirmModalProceed() {
        if (confirmModalForm) {
            confirmModalForm.dataset.confirmed = 'true';
            confirmModalForm.submit();
        }
        closeConfirmModal();
    }
</script>
