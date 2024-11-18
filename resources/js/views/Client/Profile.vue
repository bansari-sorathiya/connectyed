<template>
    <div class="mx-full shadow-connectyed rounded-xl bg-connectyed-card-light flex flex-col py-5 px-5 mb-5">
        <div class="grid grid-cols-4 gap-4">
            <label class="text-lg font-bold text-gray-900 dark:text-white mb-4 p-2">About your Profile</label>
            <span></span>
            <span></span>
            <button
                class="bg-connectyed-button-light text-connectyed-button-dark py-3 px-10"
                @click="toggleFormVisibility"
                v-if="!isFormVisible"
            >
                Edit Info
            </button>
        </div>

        <!-- Loading State -->
        <div v-if="!user || !profile" class="text-center my-5">
            <p class="text-gray-700">Loading user and profile information...</p>
        </div>

        <!-- Main Content -->
        <div class="main-content" v-else>
            <!-- Profile Display -->
            <div v-if="!isFormVisible">
                <div class="flex">
                    <!-- Profile Images Section -->
                    <div class="col-4 p-2">
                        <div class="flex flex-wrap justify-left cursor-pointer">
                            <!-- Main Image -->
                            <div class="w-[270px] h-[360px] overflow-hidden bg-gray-400">
                                <img
                                    :src="currentAvatar || '/upload/images/profiles/default.png'"
                                    alt="Main Image"
                                    class="w-full h-full object-cover"
                                />
                            </div>

                            <!-- Thumbnails -->
                            <div class="flex flex-wrap justify-left gap-2 mt-2">
                                <div
                                    class="w-[45px] h-[61px] p-1 overflow-hidden cursor-pointer"
                                    v-for="(image, index) in profile.additionalImages"
                                    :key="image" <!-- Ensure unique key -->
                                >
                                    <img
                                        :src="image"
                                        @click="updateAvatar(image)"
                                        alt="Profile Thumbnail"
                                        class="shadow-sm bg-gray-400 w-full h-full object-cover rounded"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Upload Image Buttons -->
                        <div class="flex w-full gap-2 my-2">
                            <label class="cursor-pointer flex-1">
                                <span class="text-connectyed-button-dark bg-connectyed-button-light hover:bg-connectyed-button-hover
                                    hover:text-connectyed-button-hover-dark focus:outline-none focus:ring-2 focus:ring-blue-500 px-4 py-2 w-full
                                    block text-center rounded">
                                    Upload Image 1
                                </span>
                                <input
                                    type="file"
                                    class="hidden"
                                    @change="uploadFile($event, 1)"
                                    accept="image/*"
                                />
                            </label>

                            <label class="cursor-pointer flex-1">
                                <span class="text-connectyed-button-dark bg-connectyed-button-light hover:bg-connectyed-button-hover
                                    hover:text-connectyed-button-hover-dark focus:outline-none focus:ring-2 focus:ring-blue-500 px-4 py-2 w-full
                                    block text-center rounded">
                                    Upload Image 2
                                </span>
                                <input
                                    type="file"
                                    class="hidden"
                                    @change="uploadFile($event, 2)"
                                    accept="image/*"
                                />
                            </label>
                        </div>
                    </div>

                    <!-- Profile Details Section -->
                    <div class="col-7">
                        <!-- User Name -->
                        <div class="flex flex-wrap mb-1">
                            <div class="w-full">
                                <label class="text-gray-700 text-2xl font-semibold">
                                    {{ profile.name }}
                                </label>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex flex-wrap mb-1">
                            <div class="w-full">
                                <label class="text-gray-700 text-2xl">
                                    {{ profile.city || 'Your City' }}, {{ profile.country || 'Your Country' }}
                                </label>
                            </div>
                        </div>

                        <!-- Additional Profile Information -->
                        <!-- You can expand this section with all the profile details as needed -->
                        <div class="flex flex-wrap mb-3">
                            <div class="w-full">
                                <label class="text-gray-700 text-md font-medium">
                                    Location:
                                </label>
                                <span>{{ profile.location || 'Your current location / City' }}</span>
                            </div>
                        </div>
                        <!-- Repeat similar blocks for other profile details -->
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div v-if="isFormVisible">
                <form class="w-full" @submit.prevent="postProfile" method="put">
                    <!-- Full Name -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
                                Full Name
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                type="text"
                                v-model="profile.name"
                                id="name"
                                required
                            />
                        </div>
                    </div>

                    <!-- City and State -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <!-- City -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="city">
                                City
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.city"
                                id="city"
                                type="text"
                                required
                            />
                        </div>

                        <!-- State -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="state">
                                State
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.state"
                                id="state"
                                type="text"
                                required
                            />
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="country">
                                Country
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="profile.country"
                                    id="country"
                                    required
                                >
                                    <option value="">Select Country</option>
                                    <!-- Dynamic Country Options -->
                                    <option
                                        v-for="(country, index) in countries"
                                        :key="country"
                                        :value="country"
                                    >
                                        {{ country }}
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Location -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="location">
                                Current Location (City)
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.location"
                                id="location"
                                type="text"
                                required
                            />
                        </div>
                    </div>

                    <!-- Age and Hair Color -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <!-- Age -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="age">
                                Age
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.age"
                                id="age"
                                type="number"
                                min="0"
                                required
                            />
                        </div>

                        <!-- Hair Color -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="haircolor">
                                Hair Color
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.haircolor"
                                id="haircolor"
                                type="text"
                                required
                            />
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="gender">
                            Gender
                        </label>
                        <div class="relative w-full">
                            <select
                                class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.gender"
                                id="gender"
                                required
                            >
                                <option value="">Select Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                                <!-- Add more gender options if needed -->
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Body Type, Height Feet, and Inches -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <!-- Body Type -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="bodytype">
                                Body Type
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="profile.bodytype"
                                    id="bodytype"
                                    required
                                >
                                    <option value="">Select Body Type</option>
                                    <option>Slender</option>
                                    <option>Average</option>
                                    <option>Athletic</option>
                                    <option>Curvy</option>
                                    <option>Big and Beautiful</option>
                                    <!-- Add more body types if needed -->
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Height (Feet) -->
                        <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="height">
                                Height (Feet)
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.height"
                                id="height"
                                placeholder="0"
                                min="0"
                                max="8"
                                type="number"
                                required
                            />
                        </div>

                        <!-- Height (Inches) -->
                        <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="inches">
                                Inches
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.inches"
                                id="inches"
                                placeholder="0"
                                min="0"
                                max="11"
                                type="number"
                                required
                            />
                        </div>
                    </div>

                    <!-- Marital Status and Children -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <!-- Marital Status -->
                        <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="maritalstatus">
                                Marital Status
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="profile.maritalstatus"
                                    id="maritalstatus"
                                    required
                                >
                                    <option value="">Select Status</option>
                                    <option>Single</option>
                                    <option>Separated</option>
                                    <option>Divorced</option>
                                    <!-- Add more statuses if needed -->
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Children -->
                        <div class="w-full md:w-1/4 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="children">
                                Children
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="profile.children"
                                    id="children"
                                    required
                                >
                                    <option value="">Select Number</option>
                                    <option>0</option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <!-- Add more numbers if needed -->
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Religion -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="religion">
                                Religion
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="profile.religion"
                                    id="religion"
                                    required
                                >
                                    <option value="">Select Religion</option>
                                    <option>Baha'i</option>
                                    <option>Buddhism</option>
                                    <option>Catholic</option>
                                    <option>Christian</option>
                                    <option>Confucianism</option>
                                    <option>Hinduism</option>
                                    <option>Islam</option>
                                    <option>Jainism</option>
                                    <option>Judaism</option>
                                    <option>Shinto</option>
                                    <option>Sikhism</option>
                                    <option>Taoism</option>
                                    <option>Zoroastrianism</option>
                                    <option>Other</option>
                                    <!-- Add more religions if needed -->
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Smoker and Drinker -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <!-- Smoker -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="smoker">
                                Smoker
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="profile.smoker"
                                    id="smoker"
                                    required
                                >
                                    <option value="">Select Option</option>
                                    <option :value="true">Yes</option>
                                    <option :value="false">No</option>
                                    <!-- Add more options if needed -->
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Drinker -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="drinker">
                                Drinker
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="profile.drinker"
                                    id="drinker"
                                    required
                                >
                                    <option value="">Select Option</option>
                                    <option>None</option>
                                    <option>Occasionally</option>
                                    <option>Often</option>
                                    <!-- Add more options if needed -->
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Education and Job Title -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <!-- Education -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="education">
                                Education
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.education"
                                id="education"
                                type="text"
                                required
                            />
                        </div>

                        <!-- Job Title -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="jobtitle">
                                Job Title
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.jobtitle"
                                id="jobtitle"
                                type="text"
                                required
                            />
                        </div>
                    </div>

                    <!-- Sports and Hobbies -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <!-- Sports -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="sports">
                                Sports
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.sports"
                                id="sports"
                                type="text"
                                required
                            />
                        </div>

                        <!-- Hobbies -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="hobbies">
                                Hobbies
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.hobbies"
                                id="hobbies"
                                type="text"
                                required
                            />
                        </div>
                    </div>

                    <!-- English Level and Languages -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <!-- English Level -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="english_level">
                                English Level
                            </label>
                            <div class="relative">
                                <select
                                    class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                    v-model="profile.english_level"
                                    id="english_level"
                                    required
                                >
                                    <option value="">Select Level</option>
                                    <option>Beginner</option>
                                    <option>Intermediate</option>
                                    <option>Proficient</option>
                                    <!-- Add more levels if needed -->
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Languages -->
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="languages">
                                Languages
                            </label>
                            <input
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.languages"
                                id="languages"
                                type="text"
                                required
                            />
                        </div>
                    </div>

                    <!-- Bio -->
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="bio">
                                Bio
                            </label>
                            <textarea
                                class="appearance-none block w-full border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="profile.bio"
                                id="bio"
                                rows="4"
                                required
                            ></textarea>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-wrap -mx-3 mb-6 justify-center">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="bg-connectyed-button-light text-connectyed-button-dark hover:bg-connectyed-button-hover-light hover:text-connectyed-button-hover-dark py-3 px-10 rounded"
                        >
                            {{ processing ? 'Saving...' : 'SAVE' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
// Import the countries array from countries.js
import { countries } from '../../components/countries.js';
import axios from 'axios';

export default {
    name: "Profile",
    data() {
        return {
            user: {},
            profile: {
                bio: '', // Initialize bio
                english_level: '', // Initialize english_level
                // Initialize other fields as needed
                additionalImages: [], // Ensure additionalImages is initialized
            },
            isFormVisible: false,
            processing: false,
            currentAvatar: null,
            authorization: this.$store.state.auth.authorization,
            countries: countries, // Add the imported countries to data
        };
    },
    computed: {
        // Access user and profile from Vuex store
        userFromStore() {
            return this.$store.getters['auth/user'];
        },
        profileFromStore() {
            return this.$store.getters['auth/profile'];
        },
    },
    mounted() {
        this.getProfile();
    },
    methods: {
        toggleFormVisibility() {
            this.isFormVisible = !this.isFormVisible;
        },

        /**
         * Fetch the user's profile from the backend API.
         */
        async getProfile() {
            this.processing = true;
            try {
                axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`;
                const response = await axios.get('/api/profile/getprofile');

                if (response.data.success) {
                    // Set profile and user data
                    this.profile = response.data.data;
                    this.user = response.data.data.user;

                    // Ensure additionalImages is an array
                    if (!Array.isArray(this.profile.additionalImages)) {
                        this.profile.additionalImages = [];
                    }

                    // Set current avatar
                    this.currentAvatar = this.profile.profile_image1 || '/upload/images/profiles/default.png';

                    // Update Vuex store with fetched user and profile
                    this.$store.commit('auth/SET_USER', this.user);
                    this.$store.commit('auth/SET_PROFILE', this.profile);
                } else {
                    alert('Failed to fetch profile data.');
                }
            } catch (error) {
                console.error('Error fetching profile:', error);
                alert('Failed to fetch profile.');
            } finally {
                this.processing = false;
            }
        },

        /**
         * Submit the updated profile to the backend API.
         */
        async postProfile() {
            this.processing = true;
            try {
                axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`;
                
                const payload = {
                    profile: {
                        ...this.profile,
                        name: this.profile.name || this.user.name,
                        bio: this.profile.bio || '',
                        english_level: this.profile.english_level || '',
                        // Convert smoker to boolean if it's a string
                        smoker: typeof this.profile.smoker === 'string' 
                            ? (this.profile.smoker === 'true' || this.profile.smoker === true) 
                            : this.profile.smoker,
                    }
                };

                const response = await axios.put('/api/profile/updateprofile', payload);

                if (response.data.success) {
                    // Update profile and user data with the response
                    this.profile = response.data.data;
                    this.user = response.data.data.user;

                    // Ensure additionalImages is an array
                    if (!Array.isArray(this.profile.additionalImages)) {
                        this.profile.additionalImages = [];
                    }

                    // Update Vuex store with new data
                    this.$store.commit('auth/SET_USER', this.user);
                    this.$store.commit('auth/SET_PROFILE', this.profile);

                    // Update current avatar
                    this.currentAvatar = this.profile.profile_image1 || '/upload/images/profiles/default.png';

                    this.isFormVisible = false;
                    alert('Profile updated successfully');
                } else {
                    alert('Failed to update profile.');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                alert('Failed to update profile. Please try again.');
            } finally {
                this.processing = false;
            }
        },

        /**
         * Upload a profile image.
         * @param {Event} event - The file input change event.
         * @param {Number} imageNumber - Indicates which image slot to upload (1 or 2).
         */
        async uploadFile(event, imageNumber = 1) {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append("file", file);
            formData.append("image_number", imageNumber);

            this.processing = true;
            try {
                axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`;
                const response = await axios.post('/api/profile/uploadimage', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    if (imageNumber === 1) {
                        this.profile.profile_image1 = response.data.data;
                        this.currentAvatar = response.data.data;
                    } else if (imageNumber === 2) {
                        // Assuming you have a separate field for the second image
                        if (!this.profile.additionalImages) {
                            this.profile.additionalImages = [];
                        }
                        this.profile.additionalImages.push(response.data.data);
                    }
                    // Update Vuex store with the new profile
                    this.$store.commit('auth/SET_PROFILE', this.profile);
                    alert('Image uploaded successfully');
                } else {
                    alert('Failed to upload image.');
                }
            } catch (error) {
                console.error('Error uploading image:', error);
                alert('Failed to upload image.');
            } finally {
                this.processing = false;
            }
        },

        /**
         * Update the avatar to a selected image.
         * @param {String} image - The URL of the selected image.
         */
        async updateAvatar(image) {
            this.processing = true;
            try {
                axios.defaults.headers.common.Authorization = `Bearer ${this.authorization.token}`;
                const payload = {
                    avatar: image,
                };
                const response = await axios.put('/api/profile/updateprofile', payload);
                if (response.data.success) {
                    this.profile.profile_image1 = response.data.data.profile_image1;
                    this.currentAvatar = response.data.data.profile_image1 || '/upload/images/profiles/default.png';

                    // Update Vuex store with the new profile
                    this.$store.commit('auth/SET_PROFILE', this.profile);
                    alert('Avatar updated successfully');
                } else {
                    alert('Failed to update avatar.');
                }
            } catch (error) {
                console.error('Error updating avatar:', error);
                alert('Failed to update avatar.');
            } finally {
                this.processing = false;
            }
        },
    },
};
</script>
<style scoped>
/* Add any component-specific styles here */

/* Example: Customize the appearance of the upload buttons */
.bg-connectyed-button-light {
    background-color: #4CAF50; /* Example color */
}

.text-connectyed-button-dark {
    color: #FFFFFF; /* Example color */
}

.bg-connectyed-button-hover {
    background-color: #45a049; /* Example hover color */
}

.bg-connectyed-button-hover-light {
    background-color: #66BB6A; /* Example hover light color */
}

.text-connectyed-button-hover-dark {
    color: #FFFFFF; /* Example hover text color */
}

/* Additional styles as needed */
</style>
