@if($social)
    @props(['social' => []])
@endif    

<div class="text-center text-sm text-gray-500 pb-10 pt-5">
    <div class="font-bold text-orange-hn text-lg">Hospital Nacional</div>
    <div class="mt-1 text-xs flex items-center justify-center gap-2">
        +507 2078100 
         +507 3063300
    </div>
    <div class="mt-2 flex items-center justify-center gap-3">
        <div class="w-10 h-10 bg-green-hn p-2 rounded-full hover:bg-green-600 transition-colors duration-300 flex items-center justify-center">
            <a href="https://www.instagram.com/hospitalnacional/" target="_blank" class="text-white">
                <x-lucide-instagram class="w-5 h-5" />
            </a>
        </div>
        <div class="w-10 h-10 bg-green-hn p-2 rounded-full hover:bg-green-600 transition-colors duration-300 flex items-center justify-center">
            <a href="https://www.facebook.com/HospitalNacionalPanama/?locale=es_LA" targer="_blank" class="text-white">
                <x-lucide-facebook class="w-5 h-5" />
            </a>
        </div>
        <div class="w-10 h-10 bg-green-hn p-2 rounded-full hover:bg-green-600 transition-colors duration-300 flex items-center justify-center">
            <a href="https://pa.linkedin.com/company/hospital-nacional" target="_blank" class="text-white">
                <x-lucide-linkedin class="w-5 h-5" />
            </a>
        </div>
    </div>

</div>