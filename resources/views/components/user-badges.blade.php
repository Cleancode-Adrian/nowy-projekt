@props(['user', 'size' => 'md'])

@if($user->badges && $user->badges->count() > 0)
    <div class="flex flex-wrap gap-2">
        @foreach($user->badges as $badge)
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $size === 'sm' ? 'text-xs' : 'text-sm' }}"
                  style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}"
                  title="{{ $badge->description }}">
                <span>{{ $badge->icon }}</span>
                <span>{{ $badge->name }}</span>
            </span>
        @endforeach
    </div>
@endif

