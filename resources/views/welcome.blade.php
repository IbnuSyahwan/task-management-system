<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#f5f6f0] text-zinc-950 antialiased dark:bg-zinc-950 dark:text-white">
        <main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-6 py-6 sm:px-10 lg:px-14">
            <header class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-zinc-950 text-lime-300 dark:bg-lime-300 dark:text-zinc-950">
                        <x-app-logo-icon class="size-6 fill-current" />
                    </span>
                    <span class="text-sm font-semibold tracking-tight">{{ config('app.name', 'Taskflow') }}</span>
                </a>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <flux:button variant="ghost" :href="route('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:button>
                    @else
                        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
                        @if (Route::has('register'))
                            <flux:button variant="primary" :href="route('register')" wire:navigate>{{ __('Create account') }}</flux:button>
                        @endif
                    @endauth
                </nav>
            </header>

            <section class="grid flex-1 items-center gap-14 py-16 lg:grid-cols-[1.05fr_0.95fr] lg:py-24">
                <div class="max-w-2xl">
                    <flux:badge color="lime">{{ __('A calmer way to work') }}</flux:badge>
                    <h1 class="mt-6 max-w-xl text-5xl font-semibold leading-[0.98] tracking-tight sm:text-7xl">
                        {{ __('Make the next task obvious.') }}
                    </h1>
                    <p class="mt-7 max-w-lg text-lg leading-8 text-zinc-600 dark:text-zinc-400">
                        {{ __('Taskflow gives your work a clear home, from the first idea to the satisfying final checkmark.') }}
                    </p>
                    <div class="mt-9 flex flex-wrap items-center gap-4">
                        @auth
                            <flux:button variant="primary" icon="arrow-right" :href="route('tasks.index')" wire:navigate>{{ __('Open your tasks') }}</flux:button>
                        @else
                            <flux:button variant="primary" icon="arrow-right" :href="route('register')" wire:navigate>{{ __('Start organizing') }}</flux:button>
                            <flux:link :href="route('login')" wire:navigate>{{ __('I already have an account') }}</flux:link>
                        @endauth
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-md">
                    <div class="absolute -right-5 -top-5 size-24 rounded-full border-[14px] border-amber-300/70 dark:border-amber-300/20"></div>
                    <div class="relative overflow-hidden rounded-[2rem] border border-zinc-900 bg-zinc-950 p-5 text-white shadow-2xl shadow-zinc-900/20 dark:border-zinc-700">
                        <div class="flex items-center justify-between border-b border-white/10 pb-5">
                            <div>
                                <p class="text-xs uppercase tracking-[0.2em] text-lime-300">{{ __('Today') }}</p>
                                <h2 class="mt-2 text-2xl font-semibold">{{ __('Focus board') }}</h2>
                            </div>
                            <span class="flex size-10 items-center justify-center rounded-full bg-white/10 text-lime-300">{{ __('5') }}</span>
                        </div>
                        <div class="space-y-3 py-5">
                            @foreach ([['title' => 'Polish dashboard', 'meta' => 'In progress', 'color' => 'bg-sky-300'], ['title' => 'Review requirements', 'meta' => 'Completed', 'color' => 'bg-lime-300'], ['title' => 'Prepare submission', 'meta' => 'Due Friday', 'color' => 'bg-amber-300']] as $task)
                                <div class="flex items-center gap-3 rounded-2xl bg-white/8 p-4">
                                    <span class="size-2.5 shrink-0 rounded-full {{ $task['color'] }}"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium">{{ __($task['title']) }}</p>
                                        <p class="mt-1 text-xs text-zinc-400">{{ __($task['meta']) }}</p>
                                    </div>
                                    <flux:icon name="chevron-right" class="size-4 text-zinc-500" />
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-lime-300 px-4 py-3 text-sm font-semibold text-zinc-950">
                            <span>{{ __('Progress this week') }}</span>
                            <span>{{ __('72%') }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="flex flex-col gap-3 border-t border-zinc-200 py-6 text-sm text-zinc-500 dark:border-zinc-800 sm:flex-row sm:items-center sm:justify-between">
                <span>{{ __('Plan less. Finish more.') }}</span>
                <span>{{ __('Private workspace for your work.') }}</span>
            </footer>
        </main>

        @fluxScripts
    </body>
</html>
