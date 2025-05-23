<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-black dark:text-gray-1000 leading-tight">
                {{ __('Tenton') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 text-center">
                        {{ __('Aplikacioni për Kërkimin e Ilaçeve') }}
                    </h3>

                    <form method="POST" action="{{ route('drug.search.submit') }}" id="drugSearchForm">
                        @csrf
                        <div class="mb-4">
                            <label for="ndc_codes" class="block text-sm font-medium text-gray-700 sr-only">
                                {{ __('Shkruaj kodet të ndara me presje') }}
                            </label>
                            <textarea name="ndc_codes" id="ndc_codes" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white text-gray-900 placeholder-gray-500"
                                      placeholder="{{ __('12345-6789, 11111-2222, 99999-0000') }}"
                                      required>{{ old('ndc_codes', request()->input('ndc_codes_submitted')) }}</textarea>
                            @error('ndc_codes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end items-center">
                            {{-- Spinner Element with SVG directly inside --}}
                            <div id="loadingSpinner" style="display: none;" class="inline-flex items-center text-sm text-gray-700 mr-3">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Duke kërkuar...') }}
                            </div>

                            <button type="submit" id="searchButton"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150">
                                {{ __('Kërko') }}
                            </button>
                        </div>
                    </form>

                    @if(isset($results))
                        <div class="mt-8 flow-root">
                            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">KODI NDC</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">EMRI PRODUKTIT</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">PRODHUESI</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">LLOJI I PRODUKTIT</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">BURIMI</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                        @forelse ($results as $result)
                                            <tr>
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{ $result['ndc_code'] }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $result['brand_name'] ?? ($result['generic_name'] ?? '-') }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $result['labeler_name'] ?? '-' }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $result['product_type'] ?? '-' }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                    @if($result['source'] === 'Database')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                {{ __('Database') }}
                                                            </span>
                                                    @elseif($result['source'] === 'OpenFDA')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                {{ __('OpenFDA') }}
                                                            </span>
                                                    @elseif($result['source'] === 'Nuk u Gjet')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                {{ __('Nuk u Gjet') }}
                                                            </span>
                                                    @else
                                                        <span class="text-gray-700">{{ $result['source'] }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            @if(request()->isMethod('post'))
                                                <tr>
                                                    <td colspan="5" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0 text-center">
                                                        {{ __('Asnjë rezultat nuk u gjet për kërkimin tuaj.') }}
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="5" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0 text-center">
                                                        {{ __('Bëni një kërkim për të parë rezultatet.') }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchForm = document.getElementById('drugSearchForm');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const searchButton = document.getElementById('searchButton');
            const originalButtonText = searchButton.innerHTML;

            if (searchForm && loadingSpinner && searchButton) {
                searchForm.addEventListener('submit', function (event) {
                    loadingSpinner.style.display = 'inline-flex';
                    searchButton.disabled = true;
                    searchButton.innerHTML = '{{ __("Duke kërkuar...") }}';
                });
            }
        });
    </script>
</x-app-layout>
