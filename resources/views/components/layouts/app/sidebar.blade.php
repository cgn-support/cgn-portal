<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group class="grid">

                @if (Str::contains(request()->path(), 'project'))
                    <div class="my-4 border-t">
                        <h3 class="mt-4 font-bold text-black">Account Pages</h3>
                        <flux:navlist.item icon="lifebuoy" :href="route('support')"
                            :current="request()->routeIs('support')" wire:navigate>{{ __('Helpdesk') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="calendar-days"
                            href="https://portal.test/project/0196d54e-3aaa-71ab-a858-fcde81759e8a/leads"
                            :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Leads') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="arrow-up-on-square" :href="route('dashboard')"
                            :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Reports') }}
                        </flux:navlist.item>
                    </div>
                    <div class="my-4 border-t">
                        <h3 class="mt-4 font-bold text-black">Project Reports</h3>
                        <flux:navlist.item icon="chart-bar"
                            href="https://app.seranktracker.com/projects/IXNmW4F/45dcfdcc0a2e47fc4e3622fd21c140b8/client"
                            :current="request()->routeIs('dashboard')" target="_blank">{{ __('SEO Rankings') }}
                        </flux:navlist.item>
                        <flux:navlist.item icon="map" target="_blank"
                            href="https://www.local-marketing-reports.com/location-dashboard/c567222026bcf219cee102a98dc43fd811bdd61c/lsg/view?keyword=bathroom+remodel&runId=0196e95a-b5e0-7152-8f0f-2a5676881042&timeline=6Months">
                            {{ __('Local Heatmap') }}</flux:navlist.item>
                        <flux:navlist.item icon="star" target="_blank"
                            href="https://www.local-marketing-reports.com/location-dashboard/c567222026bcf219cee102a98dc43fd811bdd61c/rm/view">
                            {{ __('Manage Reviews') }}</flux:navlist.item>
                        <flux:navlist.item icon="calendar-days"
                            :href="route('project.content', ['uuid' => $project->id ?? 'default-uuid'])"
                            :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Content Plan') }}
                        </flux:navlist.item>

                    </div>
                @endif
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="folder-git-2" href="#" target="_blank">
                {{ __('CGN Podcast') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="#" target="_blank">
                {{ __('CGN YouTube') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="#" target="_blank">
                {{ __('CGN Blog') }}
            </flux:navlist.item>
        </flux:navlist>

        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
    @livewireScripts
</body>

</html>
