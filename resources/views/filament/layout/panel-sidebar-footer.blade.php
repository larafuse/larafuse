@php
    $user = filament()->auth()->user();
    $items = filament()->getUserMenuItems();

    $profileItem = $items['profile'] ?? ($items['account'] ?? null);
    $profileItemUrl = $profileItem?->getUrl();
    $profilePage = filament()->getProfilePage();
    $hasProfileItem = filament()->hasProfile() || filled($profileItemUrl);

    $logoutItem = $items['logout'] ?? null;

    $items = \Illuminate\Support\Arr::except($items, ['account', 'logout', 'profile']);

    $fistName = explode(' ', filament()->getUserName($user));

    $sidebarCollapsible = filament()->isSidebarCollapsibleOnDesktop();
@endphp

<div class="flex flex-col py-4 pl-6 pr-10 fi-sidebar-nav growmax-theme">

    <div class="w-full h-px my-3 bg-slate-200 dark:bg-zinc-700"></div>

    <x-filament::dropdown placement="bottom-end" teleport>
        <x-slot name="trigger" class="relative">
            <button aria-label="{{ __('filament-panels::layout.actions.open_user_menu.label') }}" type="button"
                class="flex items-center">
                <x-filament-panels::avatar.user :user="$user" />

                <span
                    @if ($sidebarCollapsible) x-show="$store.sidebar.isOpen"
                        x-transition:enter="lg:transition lg:delay-100"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" @endif
                    class="flex-1 ml-3 text-sm font-medium text-gray-700 truncate fi-sidebar-item-label dark:text-gray-200">
                    {{ $fistName[0] }}
                </span>
                <svg @if ($sidebarCollapsible) x-show="$store.sidebar.isOpen"
                        x-transition:enter="lg:transition lg:delay-100"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" @endif
                    class="absolute right-0 w-4 h-4 ease-out rotate-180 -translate-x-2 fill-current group-hover:delay-150 duration-0 group-hover:duration-300"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </x-slot>

        @if ($profileItem?->isVisible() ?? true)
            @if ($hasProfileItem)
                <x-filament::dropdown.list>
                    <x-filament::dropdown.list.item :color="$profileItem?->getColor()" :icon="$profileItem?->getIcon() ??
                        (\Filament\Support\Facades\FilamentIcon::resolve('panels::user-menu.profile-item') ??
                            'heroicon-m-user-circle')" :href="$profileItemUrl ?? filament()->getProfileUrl()"
                        :target="$profileItem?->shouldOpenUrlInNewTab() ?? false ? '_blank' : null" tag="a">
                        {{ $profileItem?->getLabel() ?? (($profilePage ? $profilePage::getLabel() : null) ?? filament()->getUserName($user)) }}
                    </x-filament::dropdown.list.item>
                </x-filament::dropdown.list>
            @else
                <x-filament::dropdown.header :color="$profileItem?->getColor()" :icon="$profileItem?->getIcon() ??
                    (\Filament\Support\Facades\FilamentIcon::resolve('panels::user-menu.profile-item') ??
                        'heroicon-m-user-circle')">
                    {{ $profileItem?->getLabel() ?? filament()->getUserName($user) }}
                </x-filament::dropdown.header>
            @endif
        @endif

        @if (filament()->hasDarkMode() && !filament()->hasDarkModeForced())
            <x-filament::dropdown.list>
                <x-filament-panels::theme-switcher />
            </x-filament::dropdown.list>
        @endif

        <x-filament::dropdown.list>
            @foreach ($items as $key => $item)
                @php
                    $itemPostAction = $item->getPostAction();
                @endphp

                <x-filament::dropdown.list.item :action="$itemPostAction" :color="$item->getColor()" :href="$item->getUrl()" :icon="$item->getIcon()"
                    :method="filled($itemPostAction) ? 'post' : null" :tag="filled($itemPostAction) ? 'form' : 'a'" :target="$item->shouldOpenUrlInNewTab() ? '_blank' : null">
                    {{ $item->getLabel() }}
                </x-filament::dropdown.list.item>
            @endforeach

            <x-filament::dropdown.list.item :action="$logoutItem?->getUrl() ?? filament()->getLogoutUrl()" :color="$logoutItem?->getColor()" :icon="$logoutItem?->getIcon() ??
                (\Filament\Support\Facades\FilamentIcon::resolve('panels::user-menu.logout-button') ??
                    'heroicon-m-arrow-left-on-rectangle')" method="post"
                tag="form">
                {{ $logoutItem?->getLabel() ?? __('filament-panels::layout.actions.logout.label') }}
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>

</div>
