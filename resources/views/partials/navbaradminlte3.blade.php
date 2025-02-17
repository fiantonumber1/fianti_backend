@php
    use App\Models\Unit;
    use App\Models\Notification;

    function singkatan($str)
    {

        if ($str == "RAMS") {
            return "RAMS";
        } elseif ($str == "MTPR") {
            return "MTPR";
        } else {
            // Split string into words
            $words = explode(' ', $str);

            // Initialize abbreviation variable
            $abbreviation = '';

            // Iterate through each word to form abbreviation
            foreach ($words as $word) {
                // Add the first character of each word to the abbreviation
                $abbreviation .= strtoupper(substr($word, 0, 1));
            }

            // If abbreviation length is more than 10, limit it to 10 characters
            if (strlen($abbreviation) > 10) {
                $abbreviation = substr($abbreviation, 0, 10);
            } elseif (strlen($abbreviation) == 0) {
                // If abbreviation ends up being empty (e.g., input was an empty string), handle it as needed
                $abbreviation = 'DEFAULT'; // Example default value
            }

            // Return the abbreviation or original input if it's shorter than or equal to 10 characters
            return $abbreviation ?: $str;
        }

    }

    // Check if the user is logged in
    if (auth()->check()) {
        $userdef = auth()->user();
        $userdefrule = $userdef->rule;
        $unitsingkatan = singkatan($userdef->rule);
        $nama_divisi = $userdef->rule;

        // Search for the unit by name
        $unit = Unit::where('name', $nama_divisi)->first();

        // Fetch notifications only if the unit is found
        if ($unit) {
            $tugasdivisis = Notification::where('idunit', $unit->id)
                ->where('status', 'unread')
                ->with(['memo', 'unit']) // Misalnya, jika Anda juga butuh unit yang terkait
                ->get();
        } else {
            $tugasdivisis = collect([]);
        }
    } else {
        // Handle case when user is not authenticated, e.g., set default value or redirect
        $tugasdivisis = collect([]);
        $userdefrule = "";
        // Opsional: redirect atau beri notifikasi bahwa user harus login
        // return redirect('login'); // contoh redirect ke halaman login
    }

@endphp


<!-- Navbar -->
@include('partials.navbarpart')
<!-- /.navbar -->

<!-- Main Sidebar Container -->
@include('partials.sidebarpart')