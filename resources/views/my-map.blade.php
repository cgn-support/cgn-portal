<x-layouts.app>
    <div class="flex-1 max-md:pt-8 self-stretch mb-6">
        <div class="flex justify-between items-center">
            <h1
                class="text-3xl font-bold bg-[linear-gradient(130deg,#003E4A_0.69%,#112629_50.19%,#FC7B3E_79.69%)] bg-clip-text text-transparent">
                My Project Timeline</h1>
            <p class="mt-2 text-base">Your interactive project timeline is below.</p>
        </div>
    </div>
    <div>
        {{--    <iframe src="{{ $embedUrl }}" --}}
        {{--            width="100%" height="800px"></iframe> --}}

        <iframe src="https://view.monday.com/embed/8914334530-1c3570b8bfa7f145f7e4dfe4387903cf?r=use1" width=1100
            height=800></iframe>
    </div>
</x-layouts.app>
