<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            {{ __('Shto Ilaç të Ri Manualisht') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <form method="POST" action="{{ route('stored-drugs.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="ndc_code" class="block text-sm font-medium text-gray-700">{{ __('KODI NDC') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="ndc_code" id="ndc_code" value="{{ old('ndc_code') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('ndc_code') border-red-500 @enderror">
                            @error('ndc_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="brand_name" class="block text-sm font-medium text-gray-700">{{ __('EMRI PRODUKTIT (Brand Name)') }}</label>
                            <input type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('brand_name') border-red-500 @enderror">
                            @error('brand_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="generic_name" class="block text-sm font-medium text-gray-700">{{ __('Emri Gjenerik') }}</label>
                            <input type="text" name="generic_name" id="generic_name" value="{{ old('generic_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('generic_name') border-red-500 @enderror">
                            @error('generic_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="labeler_name" class="block text-sm font-medium text-gray-700">{{ __('PRODHUESI (Labeler Name)') }}</label>
                            <input type="text" name="labeler_name" id="labeler_name" value="{{ old('labeler_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('labeler_name') border-red-500 @enderror">
                            @error('labeler_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="product_type" class="block text-sm font-medium text-gray-700">{{ __('LLOJI I PRODUKTIT') }}</label>
                            <input type="text" name="product_type" id="product_type" value="{{ old('product_type') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('product_type') border-red-500 @enderror">
                            @error('product_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('stored-drugs.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                {{ __('Anulo') }}
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Ruaj Ilaçin') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
