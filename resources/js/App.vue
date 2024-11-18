<!-- resources/js/App.vue -->
<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
  mounted() {
    this.addGlobalMetaTags();
    this.addSchemaMarkup();
  },
  methods: {
    addGlobalMetaTags() {
      // Set the title
      document.title = "Connectyed | Premium Matchmaking for Busy Professionals";

      // Add meta description
      const description = document.createElement("meta");
      description.name = "description";
      description.content = "Discover personalized matchmaking services with Comnectyed. Connect with expert matchmakers who understand your needs as a busy professional and start your journey to meaningful relationships today.";
      document.head.appendChild(description);

      // Add Open Graph tags for social sharing
      const ogTitle = document.createElement("meta");
      ogTitle.property = "og:title";
      ogTitle.content = "Comnectyed | Premium Matchmaking for Busy Professionals";
      document.head.appendChild(ogTitle);

      const ogDescription = document.createElement("meta");
      ogDescription.property = "og:description";
      ogDescription.content = "Discover personalized matchmaking services with Comnectyed. Connect with expert matchmakers who understand your needs as a busy professional and start your journey to meaningful relationships today.";
      document.head.appendChild(ogDescription);

      const ogImage = document.createElement("meta");
      ogImage.property = "og:image";
      ogImage.content = "https://www.comnectyed.com/logo.png";
      document.head.appendChild(ogImage);

      const ogUrl = document.createElement("meta");
      ogUrl.property = "og:url";
      ogUrl.content = "https://www.comnectyed.com";
      document.head.appendChild(ogUrl);
    },

    addSchemaMarkup() {
      // Add JSON-LD schema markup
      const script = document.createElement("script");
      script.type = "application/ld+json";
      script.text = JSON.stringify({
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Comnectyed",
        "url": "https://www.comnectyed.com",
        "logo": "https://www.comnectyed.com/logo.png",
        "sameAs": [
          "https://www.facebook.com/comnectyed",
          "https://www.instagram.com/comnectyed",
          "https://www.linkedin.com/company/comnectyed"
        ],
        "contactPoint": [{
          "@type": "ContactPoint",
          "telephone": "+1-123-456-7890",
          "contactType": "Customer Service",
          "areaServed": "US",
          "availableLanguage": "English"
        }],
        "foundingDate": "2024",
        "founders": [{
          "@type": "Person",
          "name": "George [Last Name]",
          "jobTitle": "Founder & CEO",
          "description": "George is the visionary behind Comnectyed, bringing years of experience in relationship counseling and matchmaking."
        }],
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "123 Main Street",
          "addressLocality": "City",
          "addressRegion": "State",
          "postalCode": "12345",
          "addressCountry": "US"
        },
        "description": "Comnectyed connects busy professionals with expert matchmakers to find meaningful relationships through personalized services.",
        "employee": {
          "@type": "Person",
          "name": "Jane Doe",
          "jobTitle": "Lead Matchmaker",
          "description": "Jane Doe has over a decade of experience in matchmaking, specializing in high-profile clients."
        },
        "makesOffer": {
          "@type": "Offer",
          "itemOffered": {
            "@type": "Service",
            "name": "Matchmaking Service",
            "description": "Personalized matchmaking services including introduction sessions, video dates, and ongoing support."
          },
          "priceCurrency": "USD",
          "price": "Varies"
        }
      });
      document.head.appendChild(script);
    }
  }
};
</script>

<style>
/* Add any global styles here */
</style>
