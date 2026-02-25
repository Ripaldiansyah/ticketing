<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-end mb-6">
            <button wire:click="create"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-sm">
                + Tambah Template
            </button>
        </div>

        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- List Table -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-white border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Content
                            Preview</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($templates as $template)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 w-1/4">
                            {{ $template->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 space-y-1">
                            <p class="whitespace-normal leading-relaxed">{{ Str::limit($template->content, 150) }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <button wire:click="edit({{ $template->id }})"
                                class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                            <button wire:click="delete({{ $template->id }})" wire:confirm="Hapus template ini?"
                                class="text-red-600 hover:text-red-900 font-medium ml-2">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                            Belum ada template chat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $templates->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 item-center flex justify-center items-center"
        wire:click.self="closeModal">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-lg m-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                {{ $editingId ? 'Edit Template' : 'Template Baru' }}
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Template</label>
                    <input type="text" wire:model="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Contoh: Tiket Event Default">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konten Pesan</label>
                    <textarea wire:model="content" rows="8"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono text-sm"
                        placeholder="Tulis pesan Anda..."></textarea>
                    @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 p-2 rounded">
                        <strong>Available Variables:</strong><br>
                        {customer_name}, {event_name}, {event_date}, {event_venue}, {ticket_code}, {ticket_link}
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button wire:click="closeModal"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700">
                    Batal
                </button>
                <button wire:click="save" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Simpan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>