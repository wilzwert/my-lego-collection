<?php

namespace App\DataFixtures;

/**
 * @author Wilhelm Zwertvaegher
 */
class TestData
{
    /**
     * Auth module
     */
    // a known Identity id
    public const string IDENTITY1_ID = 'a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1';
    public const string IDENTITY1_EMAIL = 'user1@test.com';
    public const string IDENTITY1_USERNAME = 'user1';

    // a second known Identity id
    public const string IDENTITY2_ID = '0efa63b0-3291-4da3-9dcc-0c7ea1d538d0';
    public const string IDENTITY2_EMAIL = 'user2@test.com';
    public const string IDENTITY2_USERNAME = 'user2';

    /**
     * User module
     */
    // a known User id, associated with the Identity with id IDENTITY1_ID
    public const string USER1_ID = 'a2a2a2a2-a2a2-42a2-8a2a-a2a2a2a2a2a2';

    // a second known User id, associated with the Identity with id IDENTITY2_ID
    public const string USER2_ID = '75e0d857-f490-426e-a186-ced38f536236';


    /**
     * Notification module
     */
    // a known NotificationLog id linked to the message sent with id IDENTITY1_SENT_EMAIL_WELCOME_MESSAGE_ID
    public const string IDENTITY1_SENT_EMAIL_WELCOME_NOTIFICATION_LOG_ID = 'b2b2b2b2-a2b2-42b2-8b2b-b2b2b2b2b2b2';

    // a known message_id of a welcome notification successfully sent by email, found in a NotificationLog,
    // and linked to the Identity with id  IDENTITY1_ID
    public const string IDENTITY1_SENT_EMAIL_WELCOME_MESSAGE_ID = 'c1c1c1c1-c1c1-41c1-8c1c-c1c1c1c1c1c1';


    /**
     * CollectionManagement module
     */
    public const string COMPLETE_SET_ID = '1d160302-39c3-40d9-8c98-5f9162d2fb9d';
    public const string COMPLETE_SET2_ID = 'dea7b522-797c-4e1e-bd14-18538dc5dfa6';
    public const string INCOMPLETE_SET_ID = '651e2efe-700e-4e97-bfa6-1098c2aa1e78';

    public const string COMPLETE_USER1_SET_ID = 'ca5ccec5-1221-484d-b6ec-7dc92c96d90e';
    public const string INCOMPLETE_USER1_SET_ID = 'da448cf3-be1e-4213-8c8c-6273f6547be1';
    public const string INCOMPLETE_USER2_SET_ID = 'b5bb8902-de10-4524-89d0-c17c77eb93fc';

    public const string WANTED_USER2_SET_ID = '24864624-1746-42c0-8545-48d9629772bf';

}
