@extends('portal.layout')

@section('title', $exam->title)

@section('content')
<form id="exam-form" method="POST" action="{{ route('student.exams.submit', $exam) }}"
      x-data="{
          left: {{ (int) $secondsLeft }},
          answers: @js((object) ($attempt->answers ?? [])),
          dirty: false,
          done: false,
          get clock() { const m = Math.floor(this.left / 60), s = this.left % 60; return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0'); },
          get answered() { return Object.keys(this.answers).length; },
          save() {
              if (! this.dirty) return;
              this.dirty = false;
              fetch(@js(route('student.exams.save', $exam)), {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()), 'Accept': 'application/json' },
                  body: JSON.stringify({ answers: this.answers }),
              }).catch(() => this.dirty = true);
          },
          init() {
              setInterval(() => {
                  if (this.left > 0) { this.left--; return; }
                  if (! this.done) { this.done = true; this.$root.submit(); }
              }, 1000);
              setInterval(() => this.save(), 20000);
          }
      }"
      @submit="window.onbeforeunload = null">
    @csrf

    <div class="sticky top-16 z-20 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-100 bg-white/95 p-4 shadow-sm backdrop-blur">
        <div>
            <p class="text-xs font-semibold uppercase text-gold-600">{{ $exam->classroomSubject?->subject?->name }}</p>
            <p class="font-heading font-bold text-navy-700">{{ $exam->title }}</p>
        </div>
        <div class="flex items-center gap-4">
            <p class="text-sm text-gray-500"><span x-text="answered"></span>/{{ $questions->count() }} {{ __('answered') }}</p>
            <p class="rounded-xl px-4 py-2 font-mono text-xl font-bold" :class="left < 300 ? 'bg-red-100 text-red-700' : 'bg-navy-50 text-navy-700'" x-text="clock"></p>
        </div>
    </div>

    @if ($exam->instructions)
        <p class="rounded-2xl bg-blue-50 p-4 text-sm text-blue-900">{{ $exam->instructions }}</p>
    @endif

    <div class="space-y-4">
        @foreach ($questions as $number => $question)
            <fieldset class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <legend class="sr-only">{{ __('Question') }} {{ $number + 1 }}</legend>
                <p class="font-medium text-gray-800"><span class="mr-2 font-heading font-bold text-navy-600">{{ $number + 1 }}.</span><span class="whitespace-pre-line">{{ $question->question }}</span></p>
                <div class="mt-3 space-y-2">
                    @foreach ($question->choices() as $letter => $text)
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border px-4 py-2.5 text-sm transition" :class="answers['{{ $question->id }}'] === '{{ $letter }}' ? 'border-navy-500 bg-navy-50' : 'border-gray-200 hover:bg-gray-50'">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $letter }}" class="mt-0.5" x-model="answers['{{ $question->id }}']" @change="dirty = true">
                            <span><span class="font-semibold">{{ $letter }}.</span> {{ $text }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endforeach
    </div>

    <div class="flex justify-end">
        <button type="submit" onclick="return confirm(@js(__('Submit your answers? You cannot change them afterwards.')))" class="rounded-xl bg-green-600 px-8 py-3 font-bold text-white hover:bg-green-700">{{ __('Submit exam') }}</button>
    </div>
</form>
@endsection
