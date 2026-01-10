<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Status;
use App\Models\Priority;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\Attachment;
use App\Models\TicketEscalation;
use App\Models\ReopenRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // 1. Create Statuses
        echo "Creating ticket statuses...\n";
        $statusOpen = Status::create(['description' => 'Open', 'color' => '#10b981']);
        $statusInProgress = Status::create(['description' => 'In Progress', 'color' => '#3b82f6']);
        $statusOnHold = Status::create(['description' => 'On Hold', 'color' => '#f59e0b']);
        $statusAwaitingReply = Status::create(['description' => 'Awaiting Customer Reply', 'color' => '#f59e0b']);
        $statusEscalated = Status::create(['description' => 'Escalated', 'color' => '#ef4444']);
        $statusResolved = Status::create(['description' => 'Resolved', 'color' => '#8b5cf6']);
        $statusClosed = Status::create(['description' => 'Closed', 'color' => '#6b7280']);

        // 2. Create Priorities
        echo "Creating priority levels...\n";
        $priorityLow = Priority::create(['description' => 'Low', 'color' => '#6b7280']);
        $priorityMedium = Priority::create(['description' => 'Medium', 'color' => '#f59e0b']);
        $priorityHigh = Priority::create(['description' => 'High', 'color' => '#ef4444']);
        $priorityUrgent = Priority::create(['description' => 'Urgent', 'color' => '#dc2626']);

        // 3. Create Categories
        echo "Creating ticket categories...\n";
        $categoryBilling = Category::create(['description' => 'Billing', 'color' => '#9e1256']);
        $categoryTechnical = Category::create(['description' => 'Technical Support', 'color' => '#47053c']);
        $categoryFeature = Category::create(['description' => 'Feature Request', 'color' => '#b5a61e']);
        $categoryBugReport = Category::create(['description' => 'Bug Report', 'color' => '#ef4444']);
        $categoryAccount = Category::create(['description' => 'Account Management', 'color' => '#6b7280']);

        // 4. Create Users
        echo "Creating user accounts...\n";
        

        // Admin User
        $admin = User::create([
            'name' => 'Den Mateo',
            'email' => 'den.mateo@ticketsystem.com',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Manager Users
        $manager1 = User::create([
            'name' => 'Lancilot Tibay',
            'email' => 'lancilot.tibay@ticketsystem.com',
            'password' => Hash::make('Manager@123'),
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);

        $manager2 = User::create([
            'name' => 'Patrice Mendoza',
            'email' => 'patrice.mendoza@ticketsystem.com',
            'password' => Hash::make('Manager@123'),
            'role' => 'manager',
            'email_verified_at' => now(),
        ]);

        // Agent Users
        $agent1 = User::create([
            'name' => 'Raphael Latoy',
            'email' => 'raphael.latoy@ticketsystem.com',
            'password' => Hash::make('Agent@123'),
            'role' => 'agent',
            'email_verified_at' => now(),
        ]);

        $agent2 = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan.delacruz@ticketsystem.com',
            'password' => Hash::make('Agent@123'),
            'role' => 'agent',
            'email_verified_at' => now(),
        ]);

        $agent3 = User::create([
            'name' => 'Jose Rizal',
            'email' => 'jose.rizal@ticketsystem.com',
            'password' => Hash::make('Agent@123'),
            'role' => 'agent',
            'email_verified_at' => now(),
        ]);

        // Customer Users
        $customer1 = User::create([
            'name' => 'Andres Bonifacio',
            'email' => 'andres.bonifacio@customer.com',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $customer2 = User::create([
            'name' => 'Manny Pacquiao',
            'email' => 'manny.pacquiao@customer.com',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $customer3 = User::create([
            'name' => 'Catriona Gray',
            'email' => 'catriona.gray@customer.com',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $customer4 = User::create([
            'name' => 'Lea Salonga',
            'email' => 'lea.salonga@customer.com',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $customer3 = User::create([
            'name' => 'Robert Taylor',
            'email' => 'robert.taylor@customer.com',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $customer4 = User::create([
            'name' => 'Jennifer White',
            'email' => 'jennifer.white@customer.com',
            'password' => Hash::make('Customer@123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        echo "✓ Created 11 users (1 Admin, 2 Managers, 3 Agents, 4 Customers)\n\n";

        // 5. Create Tickets with various statuses
        echo "Creating sample tickets...\n";

        // Ticket 1: Open - Billing Issue
        $ticket1 = Ticket::create([
            'ticket_number' => '0290101',
            'title' => 'Unexpected Charge on Account',
            'description' => 'I noticed an unexpected charge of $49.99 on my account from last week. I did not authorize this charge and would like to dispute it immediately. Please investigate this matter urgently.',
            'status_id' => $statusOpen->id,
            'priority_id' => $priorityHigh->id,
            'category_id' => $categoryBilling->id,
            'user_id' => $customer1->id,
            'assigned_to' => $agent1->id,
        ]);

        // Ticket 2: In Progress - Technical Support
        $ticket2 = Ticket::create([
            'ticket_number' => '0042345',
            'title' => 'Cannot Login to Dashboard',
            'description' => 'I\'ve been trying to login to my dashboard for the past 2 hours but keep getting an error message "Invalid credentials". I\'m certain my password is correct. I recently changed my password and this issue started after that.',
            'status_id' => $statusInProgress->id,
            'priority_id' => $priorityUrgent->id,
            'category_id' => $categoryTechnical->id,
            'user_id' => $customer2->id,
            'assigned_to' => $agent2->id,
        ]);

        // Ticket 3: Resolved - Bug Report
        $ticket3 = Ticket::create([
            'ticket_number' => '1032972',
            'title' => 'Export Feature Not Working Properly',
            'description' => 'The PDF export feature is generating corrupted files. When I try to open the exported PDF, it shows an error message saying the file is damaged.',
            'status_id' => $statusResolved->id,
            'priority_id' => $priorityMedium->id,
            'category_id' => $categoryBugReport->id,
            'user_id' => $customer3->id,
            'assigned_to' => $agent1->id,
            'resolved_at' => now()->subDays(2),
        ]);

        // Ticket 4: On Hold - Feature Request
        $ticket4 = Ticket::create([
            'ticket_number' => '2134024',
            'title' => 'Request: Dark Mode Feature',
            'description' => 'Many users are requesting a dark mode option for the application. This would be especially useful for users who work during late hours. Please consider implementing this feature in the next update.',
            'status_id' => $statusOnHold->id,
            'priority_id' => $priorityLow->id,
            'category_id' => $categoryFeature->id,
            'user_id' => $customer4->id,
            'assigned_to' => $agent3->id,
        ]);

        // Ticket 5: In Progress - Account Management
        $ticket5 = Ticket::create([
            'ticket_number' => '2001005',
            'title' => 'Need to Update Account Information',
            'description' => 'I need to change my billing address and update my payment method. My company recently relocated and we need to ensure all records are current.',
            'status_id' => $statusInProgress->id,
            'priority_id' => $priorityMedium->id,
            'category_id' => $categoryAccount->id,
            'user_id' => $customer1->id,
            'assigned_to' => $agent2->id,
        ]);

        // Ticket 6: Open - Technical Support
        $ticket6 = Ticket::create([
            'ticket_number' => '2340934',
            'title' => 'Slow Performance on Mobile Devices',
            'description' => 'The application is running very slowly on my mobile device. Pages take 10+ seconds to load. I\'m using an iPhone 12 with good internet connectivity.',
            'status_id' => $statusOpen->id,
            'priority_id' => $priorityHigh->id,
            'category_id' => $categoryTechnical->id,
            'user_id' => $customer2->id,
            'assigned_to' => $agent3->id,
        ]);

        // Ticket 7: Closed - Billing
        $ticket7 = Ticket::create([
            'ticket_number' => '1920294',
            'title' => 'Invoice Missing from Account',
            'description' => 'I cannot find the invoice for my subscription from January. I need this for my accounting records.',
            'status_id' => $statusClosed->id,
            'priority_id' => $priorityLow->id,
            'category_id' => $categoryBilling->id,
            'user_id' => $customer3->id,
            'assigned_to' => $agent1->id,
            'resolved_at' => now()->subDays(5),
        ]);


        // Ticket 8: Open - Filipino Formal
        $ticket8 = Ticket::create([
            'ticket_number' => 'PH202601',
            'title' => 'Hindi gumagana ang printer',
            'description' => 'Magandang araw. Nais ko po sanang i-report na hindi gumagana ang aming printer. Sinubukan ko na pong i-on at i-off ngunit hindi pa rin ito nagpi-print. Ano po ang dapat gawin?',
            'status_id' => $statusOpen->id,
            'priority_id' => $priorityMedium->id,
            'category_id' => $categoryTechnical->id,
            'user_id' => $customer2->id,
            'assigned_to' => $agent2->id,
        ]);

        // Ticket 9: Open - Filipino Formal
        $ticket9 = Ticket::create([
            'ticket_number' => 'PH202602',
            'title' => 'Nawawala ang computer mouse',
            'description' => 'Magandang araw. Nais ko po sanang i-report na nawawala ang aking computer mouse. Mayroon po ba kayong maaaring ipahiram o ipalit?',
            'status_id' => $statusOpen->id,
            'priority_id' => $priorityLow->id,
            'category_id' => $categoryTechnical->id,
            'user_id' => $customer4->id,
            'assigned_to' => $agent3->id,
        ]);

        // Ticket 10: Open - Filipino Formal
        $ticket10 = Ticket::create([
            'ticket_number' => 'PH202603',
            'title' => 'Hindi naglo-load ang Facebook',
            'description' => 'Magandang araw. Napansin ko po na hindi naglo-load ang Facebook sa opisina. Mayroon po bang restriction sa network? Kailangan ko po ito para sa opisyal na komunikasyon.',
            'status_id' => $statusOpen->id,
            'priority_id' => $priorityHigh->id,
            'category_id' => $categoryTechnical->id,
            'user_id' => $customer1->id,
            'assigned_to' => $agent1->id,
        ]);

        // Comments for Ticket 1
        Comment::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $agent1->id,
            'content' => 'Maraming salamat po sa inyong ulat. Susuriin ko po ang inyong account at magbibigay ng update sa lalong madaling panahon.'
        ]);

        Comment::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $customer1->id,
            'content' => 'Salamat po sa mabilis na tugon. Umaasa po ako na agad itong maaayos.'
        ]);

        // Comments for Ticket 2
        Comment::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $agent2->id,
            'content' => 'Maaaring may problema po sa password. Mangyaring subukan po ang password reset upang maayos ang inyong pag-login.'
        ]);

        // Comments for Ticket 3
        Comment::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $agent1->id,
            'content' => 'Natukoy na po ang bug at ito ay naayos na. Mangyaring subukan muli ang PDF export.'
        ]);

        Comment::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $customer3->id,
            'content' => 'Maraming salamat po sa mabilis na aksyon. Maayos na po ang export feature.'
        ]);

        // Comments for Ticket 5
        Comment::create([
            'ticket_id' => $ticket5->id,
            'user_id' => $agent2->id,
            'content' => 'Mangyaring mag-login po sa inyong account at pumunta sa Settings upang ma-update ang inyong impormasyon.'
        ]);

        // Comments for Ticket 6
        Comment::create([
            'ticket_id' => $ticket6->id,
            'user_id' => $agent3->id,
            'content' => 'Mangyaring magbigay po ng karagdagang detalye tungkol sa inyong network connection upang masuri namin ang isyu.'
        ]);

        // Comments for Ticket 8
        Comment::create([
            'ticket_id' => $ticket8->id,
            'user_id' => $agent2->id,
            'content' => 'Salamat po sa inyong ulat. Susuriin ko po ang printer at magbibigay ng update sa lalong madaling panahon.'
        ]);

        Comment::create([
            'ticket_id' => $ticket8->id,
            'user_id' => $customer2->id,
            'content' => 'Maraming salamat po sa inyong tulong. Hihintayin ko po ang inyong update.'
        ]);

        // Comments for Ticket 9
        Comment::create([
            'ticket_id' => $ticket9->id,
            'user_id' => $agent3->id,
            'content' => 'Nakareceive po kami ng inyong request. Mayroon po kaming available na computer mouse na maaaring ipahiram.'
        ]);

        // Comments for Ticket 10
        Comment::create([
            'ticket_id' => $ticket10->id,
            'user_id' => $agent1->id,
            'content' => 'Ang Facebook ay maaaring naka-restrict sa opisina. Mangyaring makipag-ugnayan sa IT para sa opisyal na komunikasyon.'
        ]);

        // Comments for Ticket 10
        Comment::create([
            'ticket_id' => $ticket10->id,
            'user_id' => $agent1->id,
            'content' => 'magpaload ka po'
        ]);

        echo "✓ Added 8 comments to tickets\n\n";

        // 7. Create Attachments
        echo "Creating attachment references...\n";

        // Sample attachment paths (these would be actual image files)
        $attachmentImages = [
            ['name' => 'flowers.jpg', 'path' => 'storage/app/ticket_attachments/flowers.jpg', 'mime' => 'image/jpeg'],
            ['name' => 'laptop.jpg', 'path' => 'storage/app/ticket_attachments/laptop.png', 'mime' => 'image/png'],
            ['name' => 'working.jpg', 'path' => 'storage/app/ticket_attachments/working.jpg', 'mime' => 'image/jpeg'],
            ['name' => 'hammer.jpg', 'path' => 'storage/app/ticket_attachments/hammer.jpg', 'mime' => 'image/jpeg'],
        ];

        // Add attachments to comments
        $comment1 = Comment::where('ticket_id', $ticket1->id)->first();
        if ($comment1) {
            Attachment::create([
                'comment_id' => $comment1->id,
                'ticket_id' => $ticket1->id,
                'file_name' => 'flowers.jpg',
                'file_path' => 'ticket_attachments/flowers.jpg',
                'file_size' => 2048576,
            ]);
        }

        $comment2 = Comment::where('ticket_id', $ticket2->id)->first();
        if ($comment2) {
            Attachment::create([
                'comment_id' => $comment2->id,
                'ticket_id' => $ticket2->id,
                'file_name' => 'laptop.png',
                'file_path' => 'ticket_attachments/laptop.png',
                'file_size' => 3145728,
            ]);
        }

        $comment5 = Comment::where('ticket_id', $ticket3->id)->orderBy('id', 'desc')->first();
        if ($comment5) {
            Attachment::create([
                'comment_id' => $comment5->id,
                'ticket_id' => $ticket3->id,
                'file_name' => 'working.jpg',
                'file_path' => 'ticket_attachments/working.jpg',
                'file_size' => 1572864,
            ]);
        }

        $comment7 = Comment::where('ticket_id', $ticket6->id)->first();
        if ($comment7) {
            Attachment::create([
                'comment_id' => $comment7->id,
                'ticket_id' => $ticket6->id,
                'file_name' => 'hammer.jpg',
                'file_path' => 'ticket_attachments/hammer.jpg',
                'file_size' => 1843200,
            ]);
        }

        echo "✓ Created 4 attachment records\n\n";

        // 8. Create Escalations
        echo "Creating ticket escalations...\n";


        $escalation1 = TicketEscalation::create([
            'ticket_id' => $ticket2->id,
            'requested_by_id' => $agent2->id,
            'reason' => 'This appears to be a complex authentication issue that may require database investigation. Escalating to manager for further review.',
            'requested_at' => now()->subDays(1),
            'escalated_by_id' => $manager1->id,
            'escalated_at' => now()->subHours(20),
            'resolved_by_id' => $admin->id,
            'resolved_at' => now()->subHours(10),
            'resolution_notes' => 'Issue escalated and resolved by admin. User credentials reset.',
        ]);

        $escalation2 = TicketEscalation::create([
            'ticket_id' => $ticket6->id,
            'requested_by_id' => $agent3->id,
            'reason' => 'Potential performance issue affecting multiple users. May require system-wide optimization. Escalating for investigation.',
            'requested_at' => now()->subDays(2),
            'escalated_by_id' => $manager2->id,
            'escalated_at' => now()->subHours(30),
            'resolved_by_id' => null,
            'resolved_at' => null,
            'resolution_notes' => null,
        ]);

        echo "✓ Created 2 ticket escalations\n\n";

        // 9. Create Reopen Requests
        echo "Creating reopen requests...\n";


        $reopenRequest1 = ReopenRequest::create([
            'ticket_id' => $ticket3->id,
            'requested_by_id' => $customer3->id,
            'reason' => 'The issue appears to have returned. I\'m experiencing the same PDF corruption problem again.',
            'status' => 'accepted',
            'requested_at' => now()->subDays(1),
            'responded_by_id' => null,
            'remarks' => null,
            'responded_at' => null,
        ]);

        $reopenRequest2 = ReopenRequest::create([
            'ticket_id' => $ticket7->id,
            'requested_by_id' => $customer3->id,
            'reason' => 'I found the invoice and no longer need assistance. Please ignore this request.',
            'status' => 'declined',
            'requested_at' => now()->subDays(2),
            'responded_by_id' => $admin->id,
            'remarks' => 'Request closed as per user update.',
            'responded_at' => now()->subDays(1),
        ]);

        echo "✓ Created 2 reopen requests\n\n";

        // Print Demo Credentials
        echo "\n╔════════════════════════════════════════════════════════════════╗\n";
        echo "║         DEMO CREDENTIALS FOR TESTING                          ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n\n";


        echo "┌─ ADMIN ACCOUNT ─────────────────────────────────────────────────┐\n";
        echo "│ Pangalan:  Den Mateo                                            │\n";
        echo "│ Email:    den.mateo@ticketsystem.com                            │\n";
        echo "│ Password: Admin@123                                             │\n";
        echo "│ Role:     Admin (Buong System Access)                           │\n";
        echo "└─────────────────────────────────────────────────────────────────┘\n\n";

        echo "┌─ MANAGER ACCOUNTS ──────────────────────────────────────────────┐\n";
        echo "│ 1. Lancilot Tibay                                               │\n";
        echo "│    Email:    lancilot.tibay@ticketsystem.com                    │\n";
        echo "│    Password: Manager@123                                        │\n";
        echo "│    Role:     Manager (Pamamahala ng Team)                       │\n";
        echo "│                                                                 │\n";
        echo "│ 2. Patrice Mendoza                                              │\n";
        echo "│    Email:    patrice.mendoza@ticketsystem.com                   │\n";
        echo "│    Password: Manager@123                                        │\n";
        echo "│    Role:     Manager (Pamamahala ng Team)                       │\n";
        echo "└─────────────────────────────────────────────────────────────────┘\n\n";

        echo "┌─ AGENT ACCOUNTS ────────────────────────────────────────────────┐\n";
        echo "│ 1. Raphael Latoy                                                │\n";
        echo "│    Email:    raphael.latoy@ticketsystem.com                     │\n";
        echo "│    Password: Agent@123                                          │\n";
        echo "│    Role:     Agent (Solusyon sa Ticket)                         │\n";
        echo "│                                                                 │\n";
        echo "│ 2. Juan Dela Cruz                                               │\n";
        echo "│    Email:    juan.delacruz@ticketsystem.com                     │\n";
        echo "│    Password: Agent@123                                          │\n";
        echo "│    Role:     Agent (Solusyon sa Ticket)                         │\n";
        echo "│                                                                 │\n";
        echo "│ 3. Jose Rizal                                                   │\n";
        echo "│    Email:    jose.rizal@ticketsystem.com                        │\n";
        echo "│    Password: Agent@123                                          │\n";
        echo "│    Role:     Agent (Solusyon sa Ticket)                         │\n";
        echo "└─────────────────────────────────────────────────────────────────┘\n\n";

        echo "┌─ CUSTOMER ACCOUNTS ─────────────────────────────────────────────┐\n";
        echo "│ 1. Andres Bonifacio                                             │\n";
        echo "│    Email:    andres.bonifacio@customer.com                      │\n";
        echo "│    Password: Customer@123                                       │\n";
        echo "│    Role:     Customer (Gumagawa ng Ticket)                      │\n";
        echo "│                                                                 │\n";
        echo "│ 2. Manny Pacquiao                                               │\n";
        echo "│    Email:    manny.pacquiao@customer.com                        │\n";
        echo "│    Password: Customer@123                                       │\n";
        echo "│    Role:     Customer (Gumagawa ng Ticket)                      │\n";
        echo "│                                                                 │\n";
        echo "│ 3. Catriona Gray                                                │\n";
        echo "│    Email:    catriona.gray@customer.com                         │\n";
        echo "│    Password: Customer@123                                       │\n";
        echo "│    Role:     Customer (Gumagawa ng Ticket)                      │\n";
        echo "│                                                                 │\n";
        echo "│ 4. Lea Salonga                                                  │\n";
        echo "│    Email:    lea.salonga@customer.com                           │\n";
        echo "│    Password: Customer@123                                       │\n";
        echo "│    Role:     Customer (Gumagawa ng Ticket)                      │\n";
        echo "└─────────────────────────────────────────────────────────────────┘\n\n";

        echo "╔════════════════════════════════════════════════════════════════╗\n";
        echo "║  DEMO DATA CREATED SUCCESSFULLY!                              ║\n";
        echo "║                                                                ║\n";
        echo "║  Database Summary:                                             ║\n";
        echo "║  ✓ 1 Admin + 2 Managers + 3 Agents + 4 Customers = 11 Users   ║\n";
        echo "║  ✓ 7 Statuses (Open, In Progress, On Hold, Awaiting Customer Reply, Escalated, Resolved, Closed) ║\n";
        echo "║  ✓ 4 Priority Levels (Low, Medium, High, Urgent)              ║\n";
        echo "║  ✓ 5 Categories (Billing, Technical, Feature, Bug, Account)   ║\n";
        echo "║  ✓ 7 Sample Tickets with various statuses                      ║\n";
        echo "║  ✓ 8 Comments/Replies across tickets                           ║\n";
        echo "║  ✓ 4 Attachments (flowers, laptop, working, hammer)           ║\n";
        echo "║  ✓ 2 Ticket Escalations (with requested_by_id, escalated_by_id, resolved_by_id, timestamps) ║\n";
        echo "║  ✓ 2 Reopen Requests (with requested_by_id, responded_by_id, status, remarks, timestamps)   ║\n";
        echo "║                                                                ║\n";
        echo "║  You can now test the application with full demo data!         ║\n";
        echo "╚════════════════════════════════════════════════════════════════╝\n\n";
    }
}
