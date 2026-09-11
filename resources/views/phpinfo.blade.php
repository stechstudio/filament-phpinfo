<x-filament-panels::page>
    @foreach ($info->modules() as $module)
        <x-filament::section :heading="$module->name()" collapsible>
            @foreach ($module->groups() as $group)
                <x-filament-tables::table>
                    @if ($group->headings()->isNotEmpty())
                        <x-filament-tables::row>
                            @foreach ($group->headings() as $heading)
                                <x-filament-tables::header-cell>
                                    {{ $heading }}
                                </x-filament-tables::header-cell>
                            @endforeach
                        </x-filament-tables::row>

                        @foreach ($group->configs() as $config)
                            <x-filament-tables::row>
                                <x-filament-tables::cell :style="$loop->first ? 'max-width: 24rem; min-width: 24rem; width: 24rem; vertical-align: top' : 'vertical-align: top'">
                                    <div class="filament-tables-column-wrapper px-4 py-3">
                                        {{ $config->name() }}
                                    </div>
                                </x-filament-tables::cell>

                                <x-filament-tables::cell class="whitespace-normal" style="overflow-wrap: anywhere">
                                    <div class="filament-tables-column-wrapper px-4 py-3">
                                        {{ $config->localValue() }}
                                    </div>
                                </x-filament-tables::cell>

                                <x-filament-tables::cell class="whitespace-normal" style="overflow-wrap: anywhere">
                                    <div class="filament-tables-column-wrapper px-4 py-3">
                                        {{ $config->masterValue() }}
                                    </div>
                                </x-filament-tables::cell>
                            </x-filament-tables::row>
                        @endforeach
                    @else
                        @foreach ($group->configs() as $config)
                            <x-filament-tables::row>
                                <x-filament-tables::header-cell :style="$loop->first ? 'max-width: 24rem; min-width: 24rem; width: 24rem; vertical-align: top' : 'vertical-align: top'">
                                    {{ $config->name() }}
                                </x-filament-tables::header-cell>

                                <x-filament-tables::cell class="whitespace-normal" style="overflow-wrap: anywhere">
                                    <div class="filament-tables-column-wrapper px-4 py-3">
                                        {{ $config->localValue() }}
                                    </div>
                                </x-filament-tables::cell>
                            </x-filament-tables::row>
                        @endforeach
                    @endif
                </x-filament-tables::table>
            @endforeach
        </x-filament::section>
    @endforeach
</x-filament-panels::page>
