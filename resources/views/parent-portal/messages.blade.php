@extends('parent-portal.layout')

@section('title', 'Pesan')
@section('page-title', 'Pesan')

@section('content')
<div class="space-y-6">
    {{-- Compose --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-heading font-bold text-navy-700 mb-4">Kirim Pesan ke Wali Kelas</h2>
        <form method="POST" action="{{ route('parent.messages.send') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subjek</label>
                    <input type="text" id="subject" name="subject" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-navy-500 focus:border-navy-500"
                           placeholder="Perihal pesan...">
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                    <textarea id="message" name="message" rows="4" required
                              class="w-full rounded-lg border-gray-300 text-sm focus:ring-navy-500 focus:border-navy-500"
                              placeholder="Tulis pesan Anda..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-navy-700 text-white text-sm font-semibold rounded-lg hover:bg-navy-800 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Kirim Pesan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Messages List --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-heading font-bold text-navy-700">Riwayat Pesan</h2>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($messages ?? [] as $msg)
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $msg->is_from_parent ? 'bg-gold-100 text-gold-700' : 'bg-navy-100 text-navy-700' }}">
                                {{ $msg->is_from_parent ? 'Anda' : ($msg->sender_name ?? 'Guru') }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
                            @if(!$msg->is_read && !$msg->is_from_parent)
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            @endif
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-1">{{ $msg->subject }}</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $msg->message }}</p>
                    </div>
                </div>

                @if($msg->reply)
                <div class="mt-4 ml-6 pl-4 border-l-2 border-navy-200">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-navy-100 text-navy-700">
                            {{ $msg->reply->sender_name ?? 'Guru' }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $msg->reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600">{{ $msg->reply->message }}</p>
                </div>
                @endif
            </div>
            @empty
            <div class="p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p class="text-gray-400">Belum ada riwayat pesan.</p>
            </div>
            @endforelse
        </div>
    </div>

    @if(isset($messages) && $messages instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-6">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection
