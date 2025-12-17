<template>
    <div class="grid grid-rows-5 md:grid-cols-2 gap-3">
        <div class="w-full md:col-span-2">
            <input type="text" class="rounded-[15px] border-fields w-full" placeholder="City/Municipality" name="city">
        </div>

        <div class="w-full md:col-span-2">
            <input type="text" class="rounded-[15px] border-fields w-full" placeholder="Barangay" name="barangay">
        </div>

        <div class="w-full">
            <input type="number" class="rounded-[15px] border-fields w-full" placeholder="House Number" name="house_number">
        </div>

        <div class="w-full">
            <input type="text" class="rounded-[15px] border-fields w-full" placeholder="Street" name="street">
        </div>

        <div class="w-full md:col-span-2">
            <input type="text" class="rounded-[15px] border-fields w-full" placeholder="Village/Subdivision" name="village">
        </div>

         <div class="w-full md:col-span-2">
            <input type="number" class="rounded-[15px] border-fields w-full" placeholder="Zip Code" name="zip_code">
        </div>
    </div>
</template>