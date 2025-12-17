<template>
    <div class="grid md:grid-rows-2 md:grid-cols-3 gap-3">
        <div class="w-full">
            <input type="text" class="rounded-[15px] border-fields w-full" placeholder="First Name" name="fname">
        </div>

        <div class="w-full "> 
            <input type="text" class="rounded-[15px] border-fields w-full" placeholder="Middle Name">
        </div>

        <div class="w-full"> 
            <input type="text" class="rounded-[15px] border-fields w-full" placeholder="Last Name">
        </div>

        <div class="w-full md:col-span-2 relative group">
            <input type="date" name="bdate" class="rounded-[15px] border border-gray-300 w-full p-2 focus:border-blue-500 focus:ring-0"/>
            <!-- Tooltip -->
            <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-sm px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-all duration-200 whitespace-nowrap">
                            Enter Birthdate
            </span>
        </div>

        <div class="w-full">
            <input type="tel" class="rounded-[15px] border-fields w-full" placeholder="Landline" name="landline">
        </div>
    </div>
</template>