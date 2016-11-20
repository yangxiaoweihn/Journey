require 'BaseApi.php';
require 'UserApi.php';
require 'JourneyApi.php';


// start--------register rooter
new UserApi($app);

new JourneyApi($app);
// end--------register rooter