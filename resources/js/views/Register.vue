<!-- resources/js/views/Register.vue -->

<template>
  <div class="max-w-lg mx-auto p-4 bg-white relative">
    <h2 class="text-xl font-semibold mb-4">
      {{ form.ismatchmaker ? 'Matchmaker Registration' : 'Client Registration' }}
    </h2>

    <!-- Display Success Message -->
    <div v-if="successMessage" class="bg-green-200 text-green-800 p-4 mt-4 rounded">
      {{ successMessage }}
    </div>

    <!-- Display General Errors -->
    <div v-if="errors.general" class="bg-red-200 text-red-800 p-4 mt-4 rounded">
      {{ errors.general }}
    </div>

    <!-- Display Validation Errors -->
    <div v-if="validationErrorsList.length" class="bg-red-200 text-red-800 p-4 mt-4 rounded">
      <ul>
        <li v-for="(error, index) in validationErrorsList" :key="index">
          {{ error }}
        </li>
      </ul>
    </div>

    <!-- Matchmaker Switch -->
    <div class="font-bold text-xl mb-2">
      <div class="relative">
        <label class="flex items-center cursor-pointer mb-4 bg-orange-50 py-2 px-1 rounded-xl">
          <!-- Switch Container -->
          <div class="relative">
            <!-- Hidden checkbox input -->
            <input
              type="checkbox"
              v-model="form.ismatchmaker"
              class="sr-only"
              @click="switchMatchmaker"
            />

            <!-- Switch background -->
            <div
              :class="{
                'bg-connectyed-button-light': !form.ismatchmaker,
                'bg-connectyed-button-dark': form.ismatchmaker
              }"
              class="block w-14 h-8 rounded-full transition-colors duration-300"
            ></div>

            <!-- Switch handle -->
            <div
              class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition-transform duration-300"
              :class="{ 'translate-x-6': form.ismatchmaker }"
            ></div>
          </div>
          <span class="ml-3 text-gray-700 text-base uppercase">
            {{ form.ismatchmaker ? 'Switch to Register as a Client' : 'Switch to Register as a Matchmaker' }}
          </span>
        </label>
      </div>
      <span class="float-right" v-if="processing">
        <img class="h-5 ml-3" src="assets/images/icons/process.gif" alt="Processing..." />
      </span>
    </div>

    <!-- Step Indicator -->
    <div class="flex items-center mb-6">
      <div v-for="(step, index) in steps" :key="index">
        <div
          @click="goToStep(step)"
          class="text-center py-2 px-3 mx-1 rounded-full cursor-pointer"
          :class="{
            'bg-connectyed-button-light text-connectyed-button-dark': currentStep === step,
            'bg-gray-200 text-gray-600': currentStep !== step
          }"
        >
          {{ step }}
        </div>
      </div>
    </div>

    <form @submit.prevent="register" enctype="multipart/form-data">
      <!-- Form Steps -->

      <!-- Step 1: Account Information -->
      <div v-if="currentStep === 1">
        <h3 class="font-semibold text-lg mb-4">Account Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-1">
          <input-text
            label="Name"
            v-model="form.name"
            :required="true"
            :error="errors.name"
            maxlength="255"
          />
          <input-text
            label="Username"
            v-model="form.username"
            :required="true"
            :error="errors.username"
            maxlength="50"
          />
          <input-text
            label="Email"
            v-model="form.email"
            :required="true"
            :error="errors.email"
            maxlength="255"
            type="email"
          />
          <input-text
            label="Password"
            type="password"
            v-model="form.password"
            :required="true"
            :error="errors.password"
            maxlength="100"
          />
          <input-text
            label="Confirm Password"
            type="password"
            v-model="form.password_confirmation"
            :required="true"
            :error="errors.password_confirmation"
            maxlength="100"
          />
        </div>
      </div>

      <!-- Step 2: Profile Images & Basic Info (Matchmaker) -->
      <div v-if="currentStep === 2 && form.ismatchmaker">
        <h3 class="font-semibold text-lg mb-4">Profile Images & Basic Info</h3>

        <!-- Profile Image 1 -->
        <div class="mb-4">
          <label class="block text-gray-700">
            Upload Profile Image 1 <span class="text-red-500">*</span>
          </label>
          <input type="file" @change="onFileChange($event, 'profile_image1')" accept="image/*" required />
          <p v-if="errors.profile_image1" class="text-red-500 text-xs italic">{{ errors.profile_image1 }}</p>
        </div>

        <!-- Profile Image 2 -->
        <div class="mb-4">
          <label class="block text-gray-700">Upload Profile Image 2</label>
          <input type="file" @change="onFileChange($event, 'profile_image2')" accept="image/*" />
          <p v-if="errors.profile_image2" class="text-red-500 text-xs italic">{{ errors.profile_image2 }}</p>
        </div>

        <!-- Age -->
        <input-text
          label="Age"
          v-model="form.age"
          type="number"
          :required="true"
          :error="errors.age"
          maxlength="3"
          min="18"
          max="100"
        />

        <!-- Years of Experience -->
        <input-text
          label="Years of Experience"
          v-model="form.yearsexperience"
          type="number"
          :required="true"
          :error="errors.yearsexperience"
          min="0"
        />

        <!-- Bio -->
        <div class="mb-4">
          <label class="block text-gray-700">
            Bio <span class="text-red-500">*</span>
          </label>
          <textarea
            v-model="form.bio"
            placeholder="Tell us about yourself"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            required
          ></textarea>
          <p v-if="errors.bio" class="text-red-500 text-xs italic">{{ errors.bio }}</p>
        </div>
      </div>

      <!-- Step 2: Location Details (Client) -->
      <div v-if="currentStep === 2 && !form.ismatchmaker">
        <h3 class="font-semibold text-lg mb-4">Location Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-1">
          <input-text
            label="City"
            v-model="form.city"
            :required="true"
            :error="errors.city"
            maxlength="100"
          />
          <input-text
            label="State"
            v-model="form.state"
            :required="true"
            :error="errors.state"
            maxlength="100"
          />
          <select-option
            label="Country"
            :options="filteredCountries"
            v-model="form.country"
            :required="true"
            :error="errors.country"
            @search="handleCountrySearch"
          />
          <input-text
            label="Current Location (City)"
            v-model="form.currentLocation"
            :required="true"
            :error="errors.currentLocation"
            maxlength="100"
          />
        </div>
      </div>

      <!-- Step 3: Location Details (Matchmaker) -->
      <div v-if="currentStep === 3 && form.ismatchmaker">
        <h3 class="font-semibold text-lg mb-4">Location Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-1">
          <input-text
            label="City"
            v-model="form.city"
            :required="true"
            :error="errors.city"
            maxlength="100"
          />
          <input-text
            label="State"
            v-model="form.state"
            :required="true"
            :error="errors.state"
            maxlength="100"
          />
          <select-option
            label="Country"
            :options="filteredCountries"
            v-model="form.country"
            :required="true"
            :error="errors.country"
            @search="handleCountrySearch"
          />
          <input-text
            label="Current Location (City)"
            v-model="form.currentLocation"
            :required="true"
            :error="errors.currentLocation"
            maxlength="100"
          />
        </div>
      </div>

      <!-- Step 3: Personal Information (Clients Only) -->
      <div v-if="currentStep === 3 && !form.ismatchmaker">
        <h3 class="font-semibold text-lg mb-4">Personal Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-1">
          <input-text
            label="Age"
            v-model="form.age"
            type="number"
            :required="true"
            :error="errors.age"
            maxlength="3"
            min="1"
            max="120"
          />
          <select-option
            label="Gender"
            :options="genders"
            v-model="form.gender"
            :required="true"
            :error="errors.gender"
          />
          <input-text
            label="Hair Color"
            v-model="form.hairColor"
            :required="true"
            :error="errors.hairColor"
            maxlength="50"
          />
          <select-option
            label="Body Type"
            :options="bodyTypes"
            v-model="form.bodyType"
            :required="true"
            :error="errors.bodyType"
          />
          <select-option
              label="Ethnicity"
              :options="ethnicity"
              v-model="form.ethnicity"
              :required="true"
              :error="errors.ethnicity"
            />
          <div class="flex gap-4">
            <input-text
              label="Height (Feet)"
              v-model="form.heightFeet"
              type="number"
              :required="true"
              :error="errors.heightFeet"
              maxlength="2"
              min="1"
              max="8"
            />
            <input-text
              label="Inches"
              v-model="form.heightInches"
              type="number"
              :required="true"
              :error="errors.heightInches"
              maxlength="2"
              min="0"
              max="11"
            />
          </div>
        </div>
      </div>

      <!-- Step 4: Lifestyle Information (Clients Only) -->
      <div v-if="currentStep === 4 && !form.ismatchmaker">
        <h3 class="font-semibold text-lg mb-4">Lifestyle Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-1">
          <select-option
            label="Marital Status"
            :options="maritalStatuses"
            v-model="form.maritalStatus"
            :required="true"
            :error="errors.maritalStatus"
          />
          <select-option
            label="Children"
            :options="childrenOptions"
            v-model="form.children"
            :required="true"
            :error="errors.children"
          />
          <select-option
            label="Religion"
            :options="religions"
            v-model="form.religion"
            :required="true"
            :error="errors.religion"
          />
          <select-option
            label="Smoker"
            :options="yesNoOptions"
            v-model="form.smoker"
            :required="true"
            :error="errors.smoker"
          />
          <select-option
            label="Drinker"
            :options="drinkerOptions"
            v-model="form.drinker"
            :required="true"
            :error="errors.drinker"
          />
        </div>
      </div>

      <!-- Step 5: Professional and Hobbies (Clients Only) -->
      <div v-if="currentStep === 5 && !form.ismatchmaker">
        <h3 class="font-semibold text-lg mb-4">Professional and Hobbies</h3>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-1">
          <input-text
            label="Education"
            v-model="form.education"
            :required="true"
            :error="errors.education"
            maxlength="100"
          />
          <input-text
            label="Occupation"
            v-model="form.occupation"
            :required="true"
            :error="errors.occupation"
            maxlength="100"
          />
          <input-text
            label="Job Title"
            v-model="form.jobTitle"
            :required="true"
            :error="errors.jobTitle"
            maxlength="100"
          />
          <input-text
            label="Sports"
            v-model="form.sports"
            :required="true"
            :error="errors.sports"
            maxlength="100"
          />
          <input-text
            label="Hobbies"
            v-model="form.hobbies"
            :required="true"
            :error="errors.hobbies"
            maxlength="100"
          />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <select-option
            label="English Level"
            :options="englishLevels"
            v-model="form.englishLevel"
            :required="true"
            :error="errors.englishLevel"
          />
          <input-text
            label="Languages"
            v-model="form.languages"
            :required="true"
            :error="errors.languages"
            maxlength="100"
          />
        </div>
      </div>

      <!-- Step 6: Seeking (Clients Only) -->
      <div v-if="currentStep === 6 && !form.ismatchmaker">
        <h3 class="font-semibold text-lg mb-4">Seeking</h3>
         <div class="mb-4">
          <label class="block text-gray-700">
            Summarize what you are seeking in a match <span class="text-red-500">*</span>
          </label>
          <textarea
            v-model="form.seeking"
            placeholder="Please explain"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            required
          ></textarea>
          <p v-if="errors.seeking" class="text-red-500 text-xs italic">{{ errors.seeking }}</p>
        </div>

        <div class="flex gap-4">
          <input-text
            label="Min. Age"
            v-model="form.min_age"
            type="number"
            :required="true"
            :error="errors.age"
            maxlength="3"
            min="1"
            max="120"
          />
          <input-text
            label="Max. Age"
            v-model="form.max_age"
            type="number"
            :required="true"
            :error="errors.age"
            maxlength="3"
            min="1"
            max="120"
          />
        </div>

        <input-text
            label="Location"
            v-model="form.seeking_location"
            :required="true"
            :error="errors.currentLocation"
            maxlength="100"
          />
        <div class="flex gap-5">
          <select-option
            label="Current Kid(s)"
            :options="childrenOptions"
            v-model="form.children"
            :required="true"
            :error="errors.children"
          />
          <select-option
            label="Desired Kid(s)"
            :options="childrenOptions"
            v-model="form.desired_children"
            :required="true"
            :error="errors.desired_children"
          />
        </div>
        <input-text
            label="Hair Color"
            v-model="form.seeking_hair_color"
            :required="true"
            :error="errors.hairColor"
            maxlength="50"
          />
          <div class="flex gap-4">
            <div class="flex-1">
            <select-option
              label="Gender&nbsp;"
              :options="genders"
              v-model="form.seeking_gender"
              :required="true"
              :error="errors.gender"
            />
            </div>
            <div class="flex-1">
            <select-option
              label="Body Type&nbsp;"
              :options="bodyTypes"
              v-model="form.seeking_body_type"
              :required="true"
              :error="errors.bodyType"
            />
            </div>
          </div>
          <div class="flex gap-5">
            <div class="flex-1">
            <select-option
              label="Marital Status"
              :options="maritalStatuses"
              v-model="form.seeking_marital_status"
              :required="true"
              :error="errors.maritalStatus"
            />
            </div>
            <div class="flex-1">
            <select-option
              label="Religion"
              :options="religions"
              v-model="form.seeking_religion"
              :required="true"
              :error="errors.religion"
            />
            </div>
          </div>
          <div class="flex gap-5">
            <div class="flex-1">
            <select-option
              label="Smoker"
              :options="yesNoOptions"
              v-model="form.seeking_smoker"
              :required="true"
              :error="errors.smoker"
            />
            </div>
            <div class="flex-1">
            <select-option
              label="Drinker"
              :options="drinkerOptions"
              v-model="form.seeking_drinker"
              :required="true"
              :error="errors.drinker"
            />
            </div>
            </div>

          <div class="flex gap-5">
            <div class="flex-1">
            <select-option
              label="Ethnicity"
              :options="ethnicity"
              v-model="form.seeking_ethnicity"
              :required="true"
              :error="errors.seeking_ethnicity"
            />
            </div>
          </div>
      </div>
      <!-- Step 4: Terms and Privacy (Both Matchmaker and Client) -->
      <div v-if="(currentStep === 4 && form.ismatchmaker) || (currentStep === 7 && !form.ismatchmaker)">
        <h3 class="font-semibold text-lg mb-4">Terms and Conditions</h3>
        <div class="space-y-4 flex items-start">
          <label class="text-gray-500 text-sm mb-2 flex items-center">
            <input
              type="checkbox"
              v-model="form.privacypolicy"
              id="privacypolicy"
              name="privacypolicy"
              :required="true"
              class="mr-2 form-checkbox"
            />
            <span class="text-lg">I have read and agree to the</span>
          </label>
          <a
            @click="showPrivacy()"
            class="text-connectyed-link-dark cursor-pointer underline"
          >
            Privacy Policy
          </a>
        </div>
        <div class="space-y-4 flex items-start mt-2">
          <label class="text-gray-500 text-sm mb-2 flex items-center">
            <input
              type="checkbox"
              v-model="form.termsofuse"
              id="termsofuse"
              name="termsofuse"
              :required="true"
              class="mr-2 form-checkbox"
            />
            <span class="text-lg">I have read and agree to the</span>
          </label>
          <a
            @click="showTerm()"
            class="text-connectyed-link-dark cursor-pointer underline"
          >
            Terms of Use
          </a>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="mt-6 flex justify-between">
        <button
          v-if="currentStep > 1"
          type="button"
          class="bg-connectyed-button-pagination-light text-connectyed-button-dark py-2 px-4 rounded min-w-32 cursor-pointer"
          @click="prevStep"
        >
          Previous
        </button>
        <div class="flex-1"></div>
        <button
          v-if="currentStep < steps.length"
          type="button"
          class="bg-connectyed-button-pagination-light text-connectyed-button-dark py-2 px-4 rounded min-w-32 cursor-pointer"
          @click="nextStep"
        >
          Next
        </button>
        <button
          v-else
          class="bg-connectyed-button-light hover:bg-connectyed-button-hover-light text-connectyed-button-hover-dark py-2 px-4 rounded cursor-pointer"
          type="submit"
          :disabled="processing"
        >
          {{ processing ? "Please wait" : "Register" }}
        </button>
      </div>
    </form>

    <label class="my-4 w-full block text-center">
      Already have an account?
      <router-link class="text-connectyed-link-dark underline" :to="{ name: 'login' }">Login Now!</router-link>
    </label>

    <pdf-modal :isOpen="isModalOpen" :pdfUrl="pdfUrl" @close="closeModal" />
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import PdfModal from '../components/PdfModal.vue';
import InputText from '../components/InputText.vue';
import SelectOption from '../components/SelectOption.vue';
import { countries } from '../components/countries'; // Adjust the path as necessary

export default {
  name: "Register",
  components: {
    InputText,
    SelectOption,
    PdfModal,
  },
  data() {
    return {
      currentStep: 1,
      steps: [1, 2, 3, 4, 5, 6, 7],
      form: {
        name: "",
        username: "",
        email: "",
        password: "",
        password_confirmation: "",
        city: "",
        state: "",
        country: "",
        currentLocation: "",
        age: "",
        gender: "",
        hairColor: "",
        bodyType: "",
        heightFeet: "",
        heightInches: "",
        maritalStatus: "",
        children: "",
        religion: "",
        smoker: false, // Initialized as boolean
        drinker: "",
        education: "",
        occupation: "",
        jobTitle: "",
        sports: "",
        hobbies: "",
        englishLevel: "",
        languages: "",
        privacypolicy: false, // Initialized as boolean
        termsofuse: false,     // Initialized as boolean
        ismatchmaker: false,   // Initialized as boolean
        yearsexperience: "",
        bio: "",
        seeking: "", //New field for seeking 
        min_age: "",
        max_age: "",
        ethnicity: "",
        desired_children:"",
        seeking_location: "",
        seeking_hair_color: "",
        seeking_gender: "",
        seeking_body_type: "",
        seeking_marital_status: "",
        seeking_religion: "",
        seeking_smoker: "",
        seeking_drinker: "",
        seeking_ethnicity: "",
      },
      errors: {},
      countries: countries, // Use the imported countries array
      filteredCountries: countries, // For search functionality
      genders: ['Male', 'Female', 'Other'],
      bodyTypes: ['Slender', 'Average', 'Athletic', 'Curvy', 'Big and Beautiful'],
      maritalStatuses: ['Single', 'Separated', 'Divorced', 'Married'],
      childrenOptions: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],
      religions: [
        'Buddhism', 'Catholic', 'Christian', 'Confucianism', 'Hinduism',
        'Islam', 'Jainism', 'Judaism', 'Shinto', 'Sikhism',
        'Taoism', 'Zoroastrianism', 'Other'
      ],
      ethnicity: ['White/Caucasian','Black/African','Hispanic/Latin','Indian','Asian/Pacific Islander','Mixed race','Other'],
      yesNoOptions: ['Yes', 'No'],
      drinkerOptions: ['None', 'Occasionally', 'Often'],
      englishLevels: ['Beginner', 'Intermediate', 'Proficient'],
      successMessage: '',
      validationErrors: {},
      isModalOpen: false,
      pdfUrl: '',
      modalMode: {
        header: "",
      },
      processing: false,
      // New Data for File Uploads
      files: {
        profile_image1: null,
        profile_image2: null,
      },
    };
  },
  computed: {
    validationErrorsList() {
      // Extract all validation errors except 'general'
      return Object.keys(this.errors)
        .filter(key => this.errors[key] && key !== 'general')
        .map(key => this.errors[key]);
    }
  },
  methods: {
    ...mapActions({
      registerUser: 'auth/register'
    }),
    nextStep() {
      this.clearErrors();
      let hasError = false;

      // Validate current step fields
      switch (this.currentStep) {
        case 1:
          if (!this.form.name) {
            this.errors.name = 'Name is required';
            hasError = true;
          }
          if (!this.form.username) {
            this.errors.username = 'Username is required';
            hasError = true;
          }
          if (!this.form.email) {
            this.errors.email = 'Email is required';
            hasError = true;
          } else if (!this.validateEmail(this.form.email)) {
            this.errors.email = 'Please enter a valid email';
            hasError = true;
          }
          if (!this.form.password) {
            this.errors.password = 'Password is required';
            hasError = true;
          } else if (this.form.password.length < 6) {
            this.errors.password = 'Password must be at least 6 characters';
            hasError = true;
          }
          if (!this.form.password_confirmation) {
            this.errors.password_confirmation = 'Password confirmation is required';
            hasError = true;
          } else if (this.form.password !== this.form.password_confirmation) {
            this.errors.password_confirmation = 'Passwords do not match';
            hasError = true;
          }
          break;

        case 2:
          if (this.form.ismatchmaker) {
            // Matchmaker Registration Step 2: Profile Images & Basic Info
            if (!this.files.profile_image1) {
              this.errors.profile_image1 = 'Profile Image 1 is required';
              hasError = true;
            }
            if (!this.form.age) {
              this.errors.age = 'Age is required';
              hasError = true;
            } else if (isNaN(this.form.age) || this.form.age < 18 || this.form.age > 100) {
              this.errors.age = 'Please enter a valid age between 18 and 100';
              hasError = true;
            }
            if (this.form.yearsexperience === '' || this.form.yearsexperience === null) {
              this.errors.yearsexperience = 'Years of Experience is required';
              hasError = true;
            } else if (isNaN(this.form.yearsexperience) || this.form.yearsexperience < 0) {
              this.errors.yearsexperience = 'Please enter a valid number of years';
              hasError = true;
            }
            if (!this.form.bio) {
              this.errors.bio = 'Bio is required';
              hasError = true;
            }
          } else {
            // Client Registration Step 2: Location Details
            if (!this.form.city) {
              this.errors.city = 'City is required';
              hasError = true;
            }
            if (!this.form.state) {
              this.errors.state = 'State is required';
              hasError = true;
            }
            if (!this.form.country) {
              this.errors.country = 'Country is required';
              hasError = true;
            }
            if (!this.form.currentLocation) {
              this.errors.currentLocation = 'Current Location is required';
              hasError = true;
            }
          }
          break;

        case 3:
          if (this.form.ismatchmaker) {
            // Matchmaker Registration Step 3: Location Details
            if (!this.form.city) {
              this.errors.city = 'City is required';
              hasError = true;
            }
            if (!this.form.state) {
              this.errors.state = 'State is required';
              hasError = true;
            }
            if (!this.form.country) {
              this.errors.country = 'Country is required';
              hasError = true;
            }
            if (!this.form.currentLocation) {
              this.errors.currentLocation = 'Current Location is required';
              hasError = true;
            }
          } else {
            // Client Registration Step 3: Personal Information
            if (!this.form.age) {
              this.errors.age = 'Age is required';
              hasError = true;
            } else if (isNaN(this.form.age) || this.form.age <= 0 || this.form.age > 120) {
              this.errors.age = 'Please enter a valid age';
              hasError = true;
            }

             if (!this.form.gender) {
               this.errors.gender = 'Gender is required';
               hasError = true;
             }
             if (!this.form.hairColor) {
               this.errors.hairColor = 'Hair Color is required';
              hasError = true;
             }
             if (!this.form.bodyType) {
               this.errors.bodyType = 'Body Type is required';
              hasError = true;
             }
             if (!this.form.ethnicity) {
              this.errors.ethnicity = 'Ethnicity is required';
              hasError = true;
            }
            if (!this.form.heightFeet) {
              this.errors.heightFeet = 'Height in Feet is required';
              hasError = true;
            } else if (isNaN(this.form.heightFeet) || this.form.heightFeet <= 0 || this.form.heightFeet > 8) {
              this.errors.heightFeet = 'Please enter a valid height in feet';
              hasError = true;
            }
            if (this.form.heightInches === '' || this.form.heightInches === null) {
              this.errors.heightInches = 'Inches is required';
              hasError = true;
            } else if (isNaN(this.form.heightInches) || this.form.heightInches < 0 || this.form.heightInches >= 12) {
              this.errors.heightInches = 'Please enter a valid number of inches';
              hasError = true;
            }
          }
          break;

        case 4:
          if (this.form.ismatchmaker) {
            // Matchmaker Registration Step 4: Terms and Privacy
            if (!this.form.privacypolicy) {
              this.errors.general = 'You must agree to the Privacy Policy';
              hasError = true;
            }
            if (!this.form.termsofuse) {
              this.errors.general = 'You must agree to the Terms of Use';
              hasError = true;
            }
          } else {
             //Client Registration Step 4: Lifestyle Information
             if (!this.form.maritalStatus) {
               this.errors.maritalStatus = 'Marital Status is required';
               hasError = true;
             }
             if (this.form.children === '' || this.form.children === null) {
               this.errors.children = 'Children is required';
               hasError = true;
             }
             if (!this.form.religion) {
               this.errors.religion = 'Religion is required';
               hasError = true;
             }
            if (this.form.smoker === false && this.form.smoker === '') { // Adjusted for boolean
              this.errors.smoker = 'Smoker status is required';
              hasError = true;
            }
            if (!this.form.drinker) {
              this.errors.drinker = 'Drinker status is required';
              hasError = true;
            }
          }
          break;

        case 5:
          if (!this.form.ismatchmaker) {
            // Client Registration Step 5: Professional and Hobbies
            if (!this.form.education) {
              this.errors.education = 'Education is required';
              hasError = true;
            }
            if (!this.form.occupation) {
              this.errors.occupation = 'Occupation is required';
              hasError = true;
            }
            if (!this.form.jobTitle) {
              this.errors.jobTitle = 'Job Title is required';
              hasError = true;
            }
            if (!this.form.sports) {
              this.errors.sports = 'Sports is required';
              hasError = true;
            }
            if (!this.form.hobbies) {
              this.errors.hobbies = 'Hobbies is required';
              hasError = true;
            }
            if (!this.form.englishLevel) {
              this.errors.englishLevel = 'English Level is required';
              hasError = true;
            }
            if (!this.form.languages) {
              this.errors.languages = 'Languages is required';
              hasError = true;
            }
          }
          break;
        case 6: // New case for Seeking
            if (!this.form.ismatchmaker) {
             if (!this.form.seeking) {
               this.errors.seeking = 'Please summerize what you are seeking in your match';
               hasError = true;
             }
            if(!this.form.min_age) {
              this.errors.min_age = 'Please Enter Minimum Age';
              hasError = true;
            } else if (isNaN(this.form.min_age) || this.form.min_age < 18 || this.form.min_age > 100) {
              this.errors.age = 'Please enter a valid minimum age between 18 and 100';
              hasError = true;
            }
            if(!this.form.max_age) {
              this.errors.max_age = 'Please Enter Maximum Age';
              hasError = true;
            } else if (isNaN(this.form.max_age) || this.form.max_age < 18 || this.form.max_age > 100) {
              this.errors.age = 'Please enter a valid maximum age between 18 and 100';
              hasError = true;
            }
            if (this.form.children === '' || this.form.children === null) {
              this.errors.children = 'Current Children is required';
              hasError = true;
            }
            if (this.form.desired_children === '' || this.form.desired_children === null) {
              this.errors.desired_children = 'Desired Children is required';
              hasError = true;
            }
            if (!this.form.seeking_hair_color) {
              this.errors.seeking_hair_color = 'Hair Color is required';
              hasError = true;
            }
            if (!this.form.seeking_gender) {
              this.errors.gender = 'Gender is required';
              hasError = true;
            }
            if (!this.form.seeking_body_type) {
              this.errors.bodyType = 'Body Type is required';
              hasError = true;
            }
            if (!this.form.seeking_marital_status) {
              this.errors.maritalStatus = 'Marital Status is required';
              hasError = true;
            }
            if (!this.form.seeking_religion) {
              this.errors.religion = 'Religion is required';
              hasError = true;
            }
            if (!this.form.seeking_smoker) {
              this.errors.smoker = 'Smoker is required';
              hasError = true;
            }
            if (!this.form.seeking_drinker) {
              this.errors.drinker = 'Drinker is required';
              hasError = true;
            }
            if (!this.form.seeking_location) {
              this.errors.seeking_location = 'Location is required';
              hasError = true;
            }
            if (!this.form.seeking_ethnicity) {
              this.errors.seeking_ethnicity = 'Ethnicity is required';
              hasError = true;
            }
        }
        break;
        case 7:
          if (!this.form.ismatchmaker) {
            // Client Registration Step 6: Terms and Privacy
            if (!this.form.privacypolicy) {
              this.errors.general = 'You must agree to the Privacy Policy';
              hasError = true;
            }
            if (!this.form.termsofuse) {
              this.errors.general = 'You must agree to the Terms of Use';
              hasError = true;
            }
          }
          break;
        
        default:
          break;
      }

      if (!hasError) {
        const currentStepIndex = this.steps.indexOf(this.currentStep);
        if (currentStepIndex < this.steps.length - 1) {
          this.currentStep = this.steps[currentStepIndex + 1];
        }
      }
    },
    prevStep() {
      const currentStepIndex = this.steps.indexOf(this.currentStep);
      if (currentStepIndex > 0) {
        this.currentStep = this.steps[currentStepIndex - 1];
      }
    },
    switchMatchmaker() {
      this.form.ismatchmaker = !this.form.ismatchmaker;
      if (this.form.ismatchmaker) {
        this.steps = [1, 2, 3, 4]; // Matchmaker has fewer steps
      } else {
        this.steps = [1, 2, 3, 4, 5, 6]; // Client has all steps
      }

      // Reset currentStep if it's not in the new steps array
      if (!this.steps.includes(this.currentStep)) {
        this.currentStep = 1;
      }

      // Optionally, clear errors and form data specific to steps that are no longer visible
      this.clearErrors();
    },
    goToStep(number) {
      if (this.steps.includes(number)) {
        this.currentStep = number;
      }
    },
    showPrivacy() {
      this.modalMode.header = "Privacy Policy";
      this.pdfUrl = "/upload/pdf/privacypolicy.pdf";
      this.isModalOpen = true;
    },
    showTerm() {
      this.modalMode.header = "Terms of Use Agreement";
      this.pdfUrl = "/upload/pdf/termsofuse.pdf";
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
      this.pdfUrl = '';
    },
    onFileChange(event, imageField) {
      const file = event.target.files[0];
      this.files[imageField] = file;
    },
    async register() {
      this.processing = true;
      this.clearErrors();
      this.successMessage = '';

      // Validate all required fields before submission
      if (!this.validateAllFields()) {
        this.processing = false;
        return;
      }

      // Create a FormData object
      const formData = new FormData();

      // Append basic fields
      formData.append('name', this.form.name);
      formData.append('username', this.form.username);
      formData.append('email', this.form.email);
      formData.append('password', this.form.password);
      formData.append('password_confirmation', this.form.password_confirmation);
      
      // Explicitly convert boolean values
      formData.append('ismatchmaker', this.form.ismatchmaker === true ? '1' : '0');
      formData.append('privacypolicy', this.form.privacypolicy === true ? '1' : '0');
      formData.append('termsofuse', this.form.termsofuse === true ? '1' : '0');

      // Append location details
      formData.append('city', this.form.city);
      formData.append('state', this.form.state);
      formData.append('country', this.form.country);
      formData.append('currentLocation', this.form.currentLocation);
      formData.append('age', this.form.age);

      if (!this.form.ismatchmaker) {
        // Append all client-specific fields
        formData.append('gender', this.form.gender);
        formData.append('hairColor', this.form.hairColor);
        formData.append('bodyType', this.form.bodyType);
        formData.append('ethnicity', this.form.ethnicity); 
        formData.append('heightFeet', this.form.heightFeet);
        formData.append('heightInches', this.form.heightInches);
        formData.append('maritalStatus', this.form.maritalStatus);
        formData.append('children', this.form.children);
        formData.append('religion', this.form.religion);
        formData.append('smoker', this.form.smoker === true ? '1' : '0');
        formData.append('drinker', this.form.drinker);
        formData.append('education', this.form.education);
        formData.append('occupation', this.form.occupation);
        formData.append('jobTitle', this.form.jobTitle);
        formData.append('sports', this.form.sports);
        formData.append('hobbies', this.form.hobbies);
        formData.append('englishLevel', this.form.englishLevel);
        formData.append('languages', this.form.languages);
        formData.append('seeking', this.form.seeking); // Add this line
        formData.append('min_age', this.form.min_age); 
        formData.append('max_age', this.form.max_age); 
        formData.append('desired_children', this.form.desired_children); 
        formData.append('seeking_location', this.form.seeking_location); 
        formData.append('seeking_hair_color', this.form.seeking_hair_color); 
        formData.append('seeking_gender', this.form.seeking_gender); 
        formData.append('seeking_body_type', this.form.seeking_body_type); 
        formData.append('seeking_marital_status', this.form.seeking_marital_status); 
        formData.append('seeking_religion', this.form.seeking_religion); 
        formData.append('seeking_smoker', this.form.seeking_smoker); 
        formData.append('seeking_drinker', this.form.seeking_drinker); 
        formData.append('seeking_ethnicity', this.form.seeking_ethnicity); 
      }

      // Append matchmaker specific fields
      if (this.form.ismatchmaker) {
        formData.append('yearsexperience', this.form.yearsexperience);
        formData.append('bio', this.form.bio);
        
        // Append files
        if (this.files.profile_image1) {
          formData.append('profile_image1', this.files.profile_image1);
        }
        if (this.files.profile_image2) {
          formData.append('profile_image2', this.files.profile_image2);
        }
      }

      try {
        const response = await this.registerUser(formData);
        if (response.success === true) {
          this.successMessage = response.message;
          this.validationErrors = {};
          setTimeout(() => {
            this.$router.push({ name: "login" });
          }, 1500);
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.validationErrors = error.response.data.errors || error.response.data.message;
          this.mapValidationErrors();
        } else if (error.response && error.response.status === 500) {
          this.errors.general = 'Server error. Please try again later.';
        } else {
          this.errors.general = error.response?.data?.message || 'An unexpected error occurred.';
        }
      } finally {
        this.processing = false;
      }
    },
    // New method to validate all fields before submission
    validateAllFields() {
      if (!this.form.ismatchmaker) {
        // Validate all required client fields
        const requiredFields = {
          gender: 'Gender',
          hairColor: 'Hair Color',
          bodyType: 'Body Type',
          heightFeet: 'Height (Feet)',
          heightInches: 'Height (Inches)',
          maritalStatus: 'Marital Status',
          children: 'Children',
          religion: 'Religion',
          smoker: 'Smoker',
          drinker: 'Drinker',
          education: 'Education',
          occupation: 'Occupation',
          jobTitle: 'Job Title',
          sports: 'Sports',
          hobbies: 'Hobbies',
          englishLevel: 'English Level',
          languages: 'Languages',
          seeking: 'Seeking',
        };

        for (const [field, label] of Object.entries(requiredFields)) {
          if (!this.form[field]) {
            this.errors[field] = `${label} is required`;
            return false;
          }
        }
      }

      // Always validate common fields
      const commonFields = {
        name: 'Name',
        username: 'Username',
        email: 'Email',
        password: 'Password',
        city: 'City',
        state: 'State',
        country: 'Country',
        currentLocation: 'Current Location',
        age: 'Age',
      };

      for (const [field, label] of Object.entries(commonFields)) {
        if (!this.form[field]) {
          this.errors[field] = `${label} is required`;
          return false;
        }
      }

      // Additional validations can be added here if necessary

      return true;
    },
    mapValidationErrors() {
      for (const [key, messages] of Object.entries(this.validationErrors)) {
        if (this.errors.hasOwnProperty(key)) {
          this.errors[key] = messages[0]; // Display the first error message
        } else {
          // Handle general errors
          this.errors.general = messages[0];
        }
      }
    },
    clearErrors() {
      this.errors = {};
      this.successMessage = '';
    },
    validateEmail(email) {
      const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\]\\.,;:\s@"]+\.)+[^<>()[\]\\.,;:\s@"]{2,})$/i;
      return re.test(String(email).toLowerCase());
    },
    handleCountrySearch(query) {
      if (!query) {
        this.filteredCountries = this.countries;
      } else {
        const lowerQuery = query.toLowerCase();
        this.filteredCountries = this.countries.filter(country =>
          country.toLowerCase().includes(lowerQuery)
        );
      }
    }
  },
};
</script>

<style scoped>
/* Component-specific styles */

/* Switch Handle */
.dot {
  transition: transform 0.3s;
}

/* Error Message Styles */
.bg-red-200 {
  background-color: #fee2e2;
}
.text-red-800 {
  color: #991b1b;
}

/* Success Message Styles */
.bg-green-200 {
  background-color: #d1fae5;
}
.text-green-800 {
  color: #065f46;
}

/* Checkbox Styles */
.form-checkbox {
  @apply h-4 w-4 text-blue-600 border-gray-300 rounded;
}

/* Optional: Add custom styles for error messages or other elements if needed */
</style>
