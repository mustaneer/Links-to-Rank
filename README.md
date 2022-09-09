# Links-to-Rank
 Plugin will get user data from wpforms entry and calculate using MOZ API. [wc_links_estimation] shortcode will be used to show results.

# Plugin Description 

Recently one of Search Marketing Agency had a very interesting requirement. They needed a calculator in which a client can calculate an approximate number of referrals links their website needs to rank a keyword. This was a challenging task for me that how this could be achieved. Then after a detailed discussion, we came to a solution in which we decided to use MOZ API. So, I created a user interface using WP FORMS. In which in the first step users will enter his page url which they wanted to rank. In the second step, the users will enter their competitor website links minimum of 3 and a maximum of 5. After receiving all this information, I sent a MOZ API request to get the referral domain count, page authority, and domain authority of each competitor website URL. So, at the end by calculating the total average referral of competitor's domains and then by adding 20% buffer in average result we showed the results to the client. I also created a WP Forms custom smart tag to show results right form submission and also created a shortcode to show the result on a separate page. 

The live active plugin could be seen here https://www.clickintelligence.co.uk/how-many-links-to-rank/  and a sample of the end result could be seen here https://clickintelligence.dev/seo-tools/results/?entry_id=190&entry_hash=d447457ca62d8cf42d5b0e596436879a610a84e2#038;entry_hash=d447457ca62d8cf42d5b0e596436879a610a84e2
