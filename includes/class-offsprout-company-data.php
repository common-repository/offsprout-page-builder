<?php

/**
 * Handles logic for site data properties.
 *
 * @since 1.0
 */
final class Offsprout_Company_Data {

    static function build_site_data(){
        global $ocb_site_data;

        if( ! $ocb_site_data ){
            $ocb_site_data = get_option( 'ocb_site_settings' );
        }

        return $ocb_site_data;
    }

    static function get_site_data( $option, $property ){
        $data = self::build_site_data();

        if( isset( $data[$option] ) && isset( $data[$option][$property] ) ){
            return $data[$option][$property];
        }

        return '';
    }

    static function name( $settings ){
        $name = self::get_site_data( 'company_name', 'text' );

        return Offsprout_Connector_Shortcode::return_value_or_default( $name, 'No company name set' );
    }

    static function email( $settings ){
        $linked = isset( $settings->linked ) ? $settings->linked : false;

        $email = self::get_site_data( 'company_email', 'text' );

        if( $linked )
            $email = "<a href='mailto:$email'>$email</a>";

        return Offsprout_Connector_Shortcode::return_value_or_default( $email, 'No company email set' );
    }

    static function phone( $settings ){

        $separator = isset( $settings->separator ) ? $settings->separator : '-';
        $linked = isset( $settings->linked ) ? $settings->linked : false;
        $parentheses = isset( $settings->parentheses ) ? $settings->parentheses : false;

        $phone_number = self::get_site_data( 'company_phone', 'text' );

        if( $phone_number == false )
            return '';

        $display_number = $phone_number;

        if( strpos( $display_number, '-' ) !== false ) {

            $number_parts = explode( '-', $phone_number );

            if ( $parentheses )
                $display_number = '(' . $number_parts[0] . ') ' . $number_parts[1] . $separator . $number_parts[2];
            else
                $display_number = implode( $separator, $number_parts );

        }

        $phone_number = preg_replace( "/[^0-9]/", "", $phone_number );

        $phone = $display_number;

        if( $linked && $phone )
            $phone = "<a href='tel:$phone_number'>$display_number</a>";

        return Offsprout_Connector_Shortcode::return_value_or_default( $phone, 'No company phone set' );

    }

    static function address( $settings ){

        $include_name = isset( $settings->include_name ) ? $settings->include_name : false;
        $include_country = isset( $settings->include_country ) ? $settings->include_country : false;
        $one_line = isset( $settings->one_line ) ? $settings->one_line : false;

        $company_name = self::get_site_data( 'company_name', 'text' );
        $street1 = self::get_site_data( 'company_street1', 'text' );
        $street2 = self::get_site_data( 'company_street2', 'text' );
        $city = self::get_site_data( 'company_city', 'text' );
        $state = self::get_site_data( 'company_state', 'text' );
        $zip = self::get_site_data( 'company_zip', 'text' );
        $country = self::get_site_data( 'company_country', 'text' );
        $break = '<br />';

        $address = '';

        if( $one_line == true )
            $break = ', ';

        if( $include_name == true )
            $address .= $company_name . $break;

        $address .= $street1 . $break;

        if( $street2 )
            $address .= $street2 . $break;

        if( $city && $state )
            $address .= $city . ', ' . $state;
        else
            $address .= $city . $state;

        if( ( $city || $state ) && $zip )
            $address .= ' ';

        if( $zip )
            $address .= $zip;

        if( $include_country )
            $address .= $break . $country;

        if( ! $address )
            $address = 'No Address Set';

        return $address;

    }

    static function street1( $settings ){
        $street1 = self::get_site_data( 'street1', 'text' );

        return Offsprout_Connector_Shortcode::return_value_or_default( $street1, 'No street set' );
    }

    static function street2( $settings ){
        $street2 = self::get_site_data( 'street2', 'text' );

        return Offsprout_Connector_Shortcode::return_value_or_default( $street2, 'No street set' );
    }

    static function city( $settings ){
        return Offsprout_Connector_Shortcode::return_value_or_default( self::get_site_data( 'city', 'text' ), 'No city set' );
    }

    static function state( $settings ){
        return Offsprout_Connector_Shortcode::return_value_or_default( self::get_site_data( 'state', 'text' ), 'No state set' );
    }

    static function zip( $settings ){
        return Offsprout_Connector_Shortcode::return_value_or_default( self::get_site_data( 'zip', 'text' ), 'No zip set' );
    }

    static function country( $settings ){
        return Offsprout_Connector_Shortcode::return_value_or_default( self::get_site_data( 'country', 'text' ), 'No country set' );
    }

    public static function state_abbrev_to_full( $abbreviation ){

        $array = array(
            'AL'=>'Alabama',
            'AK'=>'Alaska',
            'AZ'=>'Arizona',
            'AR'=>'Arkansas',
            'CA'=>'California',
            'CO'=>'Colorado',
            'CT'=>'Connecticut',
            'DE'=>'Delaware',
            'DC'=>'District of Columbia',
            'FL'=>'Florida',
            'GA'=>'Georgia',
            'HI'=>'Hawaii',
            'ID'=>'Idaho',
            'IL'=>'Illinois',
            'IN'=>'Indiana',
            'IA'=>'Iowa',
            'KS'=>'Kansas',
            'KY'=>'Kentucky',
            'LA'=>'Louisiana',
            'ME'=>'Maine',
            'MD'=>'Maryland',
            'MA'=>'Massachusetts',
            'MI'=>'Michigan',
            'MN'=>'Minnesota',
            'MS'=>'Mississippi',
            'MO'=>'Missouri',
            'MT'=>'Montana',
            'NE'=>'Nebraska',
            'NV'=>'Nevada',
            'NH'=>'New Hampshire',
            'NJ'=>'New Jersey',
            'NM'=>'New Mexico',
            'NY'=>'New York',
            'NC'=>'North Carolina',
            'ND'=>'North Dakota',
            'OH'=>'Ohio',
            'OK'=>'Oklahoma',
            'OR'=>'Oregon',
            'PA'=>'Pennsylvania',
            'RI'=>'Rhode Island',
            'SC'=>'South Carolina',
            'SD'=>'South Dakota',
            'TN'=>'Tennessee',
            'TX'=>'Texas',
            'UT'=>'Utah',
            'VT'=>'Vermont',
            'VA'=>'Virginia',
            'WA'=>'Washington',
            'WV'=>'West Virginia',
            'WI'=>'Wisconsin',
            'WY'=>'Wyoming',
            'BC'=>'British Columbia',
            'ON'=>'Ontario',
            'NL'=>'Newfoundland and Labrador',
            'NS'=>'Nova Scotia',
            'PE'=>'Prince Edward Island',
            'NB'=>'New Brunswick',
            'QC'=>'Quebec',
            'MB'=>'Manitoba',
            'SK'=>'Saskatchewan',
            'AB'=>'Alberta',
            'NT'=>'Northwest Territories',
            'NU'=>'Nunavut',
            'YT'=>'Yukon Territory'
        );

        $return = isset( $array[$abbreviation] ) ? $array[$abbreviation] : $abbreviation;

        return $return;
    }

    public static function state_full_to_abbrev( $full ){

        $array = array(
            'Alabama'=>'AL',
            'Alaska'=>'AK',
            'Arizona'=>'AZ',
            'Arkansas'=>'AR',
            'California'=>'CA',
            'Colorado'=>'CO',
            'Connecticut'=>'CT',
            'Delaware'=>'DE',
            'District of Columbia'=>'DC',
            'Florida'=>'FL',
            'Georgia'=>'GA',
            'Hawaii'=>'HI',
            'Idaho'=>'ID',
            'Illinois'=>'IL',
            'Indiana'=>'IN',
            'Iowa'=>'IA',
            'Kansas'=>'KS',
            'Kentucky'=>'KY',
            'Louisiana'=>'LA',
            'Maine'=>'ME',
            'Maryland'=>'MD',
            'Massachusetts'=>'MA',
            'Michigan'=>'MI',
            'Minnesota'=>'MN',
            'Mississippi'=>'MS',
            'Missouri'=>'MO',
            'Montana'=>'MT',
            'Nebraska'=>'NE',
            'Nevada'=>'NV',
            'New Hampshire'=>'NH',
            'New Jersey'=>'NJ',
            'New Mexico'=>'NM',
            'New York'=>'NY',
            'North Carolina'=>'NC',
            'North Dakota'=>'ND',
            'Ohio'=>'OH',
            'Oklahoma'=>'OK',
            'Oregon'=>'OR',
            'Pennsylvania'=>'PA',
            'Rhode Island'=>'RI',
            'South Carolina'=>'SC',
            'South Dakota'=>'SD',
            'Tennessee'=>'TN',
            'Texas'=>'TX',
            'Utah'=>'UT',
            'Vermont'=>'VT',
            'Virginia'=>'VA',
            'Washington'=>'WA',
            'West Virginia'=>'WV',
            'Wisconsin'=>'WI',
            'Wyoming'=>'WY',
            'British Columbia'=>'BC',
            'Ontario'=>'ON',
            'Newfoundland and Labrador'=>'NL',
            'Nova Scotia'=>'NS',
            'Prince Edward Island'=>'PE',
            'New Brunswick'=>'NB',
            'Quebec'=>'QC',
            'Manitoba'=>'MB',
            'Saskatchewan'=>'SK',
            'Alberta'=>'AB',
            'Northwest Territories'=>'NT',
            'Nunavut'=>'NU',
            'Yukon Territory'=>'YT',
        );

        $return = isset( $array[$full] ) ? $array[$full] : $full;

        return $return;
    }

}