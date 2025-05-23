<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Ilaçet e Ruajtura në Databazë') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6 flex justify-end">
                        <a href="{{ route('stored-drugs.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Shto Ilaç të Ri') }}
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md border border-green-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($drugs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">KODI NDC</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">EMRI PRODUKTIT</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">PRODHUESI</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">LLOJI I PRODUKTIT</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">VEPRIMET</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                @foreach ($drugs as $drug)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{ $drug->ndc_code }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $drug->brand_name ?? ($drug->generic_name ?? '-') }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $drug->labeler_name ?? '-' }}</td>
                                        {{-- Added LLOJI I PRODUKTIT data cell --}}
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ $drug->product_type ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">
                                            <form action="{{ route('stored-drugs.destroy', $drug->id) }}" method="POST" onsubmit="return confirm('Jeni të sigurt që doni ta fshini këtë ilaç?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Fshij</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($drugs->hasPages())
                        <div class="mt-6">
                            {{ $drugs->links() }}
                        </div>
                        @endif
                    @else
                        <p class="text-center text-gray-500 py-8">{{ __('Nuk ka ilaçe të ruajtura në databazë akoma.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
