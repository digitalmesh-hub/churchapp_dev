<?php

namespace common\helpers;

use Yii;

/**
 * CacheHelper provides centralized cache management for application data
 * This ensures consistent cache handling across the application
 */
class CacheHelper
{
    /**
     * Default cache duration in seconds (24 hours)
     */
    const CACHE_DURATION = 86400;

    /**
     * Cache key prefixes for different data types
     */
    const PREFIX_TITLES = 'titles';
    const PREFIX_ALL_TITLES = 'all_titles';
    const PREFIX_STAFF_DESIGNATION = 'staff_designation';
    const PREFIX_COMMITTEES = 'committees';
    const PREFIX_FAMILY_UNITS = 'family_units';
    const PREFIX_EVENTS = 'events';
    
    /**
     * Generic method to get or set cached data
     * @param string $key Cache key
     * @param callable $callback Function to fetch data if not cached
     * @param int $duration Cache duration in seconds (default: 1 hour)
     * @return mixed
     */
    public static function getOrSet($key, $callback, $duration = self::CACHE_DURATION)
    {
        return Yii::$app->cache->getOrSet($key, $callback, $duration);
    }
    
    /**
     * Clear specific cache by key
     * @param string $key Cache key to clear
     * @return bool
     */
    public static function clear($key)
    {
        return Yii::$app->cache->delete($key);
    }
    
    /**
     * Clear multiple cache keys at once
     * @param array $keys Array of cache keys to clear
     * @return bool
     */
    public static function clearMultiple($keys)
    {
        $result = true;
        foreach ($keys as $key) {
            if (!self::clear($key)) {
                $result = false;
            }
        }
        return $result;
    }
    
    /**
     * Build cache key for institution-specific data
     * @param string $prefix Cache key prefix
     * @param int $institutionId Institution ID
     * @param string $suffix Optional suffix for the key
     * @return string
     */
    public static function buildKey($prefix, $institutionId, $suffix = '')
    {
        $key = $prefix . '_' . $institutionId;
        if (!empty($suffix)) {
            $key .= '_' . $suffix;
        }
        return $key;
    }
    
    /**
     * Build cache key for global data (not institution-specific)
     * @param string $prefix Cache key prefix
     * @param string $suffix Optional suffix for the key
     * @return string
     */
    public static function buildGlobalKey($prefix, $suffix = '')
    {
        $key = $prefix;
        if (!empty($suffix)) {
            $key .= '_' . $suffix;
        }
        return $key;
    }
    
    /**
     * Clear all application cache
     * Use this only when necessary (e.g., system-wide updates)
     * @return bool
     */
    public static function clearAllCache()
    {
        return Yii::$app->cache->flush();
    }
    
    // ==================== TITLE CACHE METHODS ====================
    
    /**
     * Get titles from cache or database
     * @param int $institutionId
     * @param callable $callback Function to fetch titles from database
     * @return array
     */
    public static function getTitles($institutionId, $callback)
    {
        $key = self::buildKey(self::PREFIX_TITLES, $institutionId);
        return self::getOrSet($key, $callback);
    }

    /**
    * Get all titles (including inactive) from cache or database
    * @param int $institutionId
    * @param callable $callback Function to fetch all titles from database
    * @return array
    */
    public static function getAllTitles($institutionId, $callback)
    {
        $key = self::buildKey(self::PREFIX_ALL_TITLES, $institutionId);
        return self::getOrSet($key, $callback);
    }

    /**
     * Clear all titles cache for specific institution
     * @param int $institutionId
     * @return bool
     */
    public static function clearAllTitlesCache($institutionId)
    {
        $key = self::buildKey(self::PREFIX_ALL_TITLES, $institutionId);
        return self::clear($key);
    }
    
    /**
     * Clear title cache for specific institution
     * @param int $institutionId
     * @return bool
     */
    public static function clearTitlesCache($institutionId)
    {
        self::clearAllTitlesCache($institutionId);
        $key = self::buildKey(self::PREFIX_TITLES, $institutionId);
        return self::clear($key);
    }
    
    // ==================== STAFF DESIGNATION CACHE METHODS ====================
    
    /**
     * Get staff designations from cache or database
     * @param int $institutionId
     * @param callable $callback Function to fetch staff designations from database
     * @return array
     */
    public static function getStaffDesignations($institutionId, $callback)
    {
        $key = self::buildKey(self::PREFIX_STAFF_DESIGNATION, $institutionId);
        return self::getOrSet($key, $callback);
    }
    
    /**
     * Clear staff designation cache for specific institution
     * @param int $institutionId
     * @return bool
     */
    public static function clearStaffDesignationsCache($institutionId)
    {
        $key = self::buildKey(self::PREFIX_STAFF_DESIGNATION, $institutionId);
        return self::clear($key);
    }
    
    // ==================== COMMITTEE CACHE METHODS ====================
    
    /**
     * Get committees from cache or database
     * @param int $institutionId
     * @param callable $callback Function to fetch committees from database
     * @return array
     */
    public static function getCommittees($institutionId, $callback)
    {
        $key = self::buildKey(self::PREFIX_COMMITTEES, $institutionId);
        return self::getOrSet($key, $callback);
    }
    
    /**
     * Clear committee cache for specific institution
     * @param int $institutionId
     * @return bool
     */
    public static function clearCommitteesCache($institutionId)
    {
        $key = self::buildKey(self::PREFIX_COMMITTEES, $institutionId);
        return self::clear($key);
    }
    
    // ==================== FAMILY UNIT CACHE METHODS ====================
    
    /**
     * Get family units from cache or database
     * @param int $institutionId
     * @param callable $callback Function to fetch family units from database
     * @return array
     */
    public static function getFamilyUnits($institutionId, $callback)
    {
        $key = self::buildKey(self::PREFIX_FAMILY_UNITS, $institutionId);
        return self::getOrSet($key, $callback);
    }
    
    /**
     * Clear family unit cache for specific institution
     * @param int $institutionId
     * @return bool
     */
    public static function clearFamilyUnitsCache($institutionId)
    {
        $key = self::buildKey(self::PREFIX_FAMILY_UNITS, $institutionId);
        return self::clear($key);
    }
}
