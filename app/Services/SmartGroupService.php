<?php

namespace App\Services;

use App\Models\ContactGroup;
use App\Models\MessageTemplate;

class SmartGroupService
{
    /**
     * Get smart group suggestions based on message content
     */
    public function getSuggestedGroups($content, $userId = null)
    {
        $content = strtolower($content);
        $suggestions = [];
        
        // Get all active groups
        $query = ContactGroup::active();
        if ($userId) {
            $query->where('user_id', $userId);
        }
        $groups = $query->get();
        
        foreach ($groups as $group) {
            $relevanceScore = $group->getRelevanceScore($content);
            if ($relevanceScore > 0) {
                $suggestions[] = [
                    'group' => $group,
                    'score' => $relevanceScore,
                    'reason' => $this->getRelevanceReason($group, $content)
                ];
            }
        }
        
        // Sort by relevance score (highest first)
        usort($suggestions, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return $suggestions;
    }
    
    /**
     * Get smart group combinations based on message content
     */
    public function getGroupCombinations($content, $userId = null)
    {
        $suggestions = $this->getSuggestedGroups($content, $userId);
        
        $combinations = [];
        
        // Single high-relevance groups
        foreach ($suggestions as $suggestion) {
            if ($suggestion['score'] >= 2) {
                $combinations[] = [
                    'name' => $suggestion['group']->name,
                    'groups' => [$suggestion['group']->id],
                    'reason' => $suggestion['reason'],
                    'score' => $suggestion['score']
                ];
            }
        }
        
        // Smart combinations based on content type
        $contentType = $this->detectContentType($content);
        $combinations = array_merge($combinations, $this->getTypeBasedCombinations($contentType, $userId));
        
        return $combinations;
    }
    
    /**
     * Detect content type for smart targeting
     */
    private function detectContentType($content)
    {
        $types = [
            'emergency' => ['urgent', 'danger', 'evacuation', 'safety', 'alert', 'crisis'],
            'weather' => ['weather', 'météo', 'tempête', 'sable', 'pluie', 'vent', 'chaleur'],
            'security' => ['security', 'sécurité', 'police', 'crime', 'safety', 'investigation'],
            'health' => ['health', 'santé', 'medical', 'hospital', 'doctor', 'vaccination'],
            'infrastructure' => ['infrastructure', 'électricité', 'eau', 'route', 'maintenance'],
            'agriculture' => ['agriculture', 'ferme', 'récolte', 'plantation', 'crops'],
            'community' => ['community', 'réunion', 'assemblée', 'announcement', 'meeting']
        ];
        
        foreach ($types as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($content, $keyword)) {
                    return $type;
                }
            }
        }
        
        return 'general';
    }
    
    /**
     * Get type-based group combinations
     */
    private function getTypeBasedCombinations($contentType, $userId)
    {
        $combinations = [];
        
        switch ($contentType) {
            case 'emergency':
                $combinations[] = [
                    'name' => 'Emergency Response Team',
                    'groups' => $this->getGroupIdsByCategories(['emergency', 'officials', 'security'], $userId),
                    'reason' => 'Emergency requires immediate response from all authorities',
                    'score' => 5
                ];
                break;
                
            case 'weather':
                $combinations[] = [
                    'name' => 'Weather-Affected Groups',
                    'groups' => $this->getGroupIdsByCategories(['farmers', 'transport', 'residents'], $userId),
                    'reason' => 'Weather alerts affect farming, transport, and general population',
                    'score' => 4
                ];
                break;
                
            case 'security':
                $combinations[] = [
                    'name' => 'Security Network',
                    'groups' => $this->getGroupIdsByCategories(['security', 'officials', 'residents'], $userId),
                    'reason' => 'Security incidents require coordinated response',
                    'score' => 4
                ];
                break;
                
            case 'health':
                $combinations[] = [
                    'name' => 'Health & Community',
                    'groups' => $this->getGroupIdsByCategories(['health', 'elderly', 'residents'], $userId),
                    'reason' => 'Health updates affect vulnerable populations and general community',
                    'score' => 3
                ];
                break;
                
            case 'infrastructure':
                $combinations[] = [
                    'name' => 'Infrastructure Impact',
                    'groups' => $this->getGroupIdsByCategories(['business', 'residents', 'officials'], $userId),
                    'reason' => 'Infrastructure issues affect businesses and residents',
                    'score' => 3
                ];
                break;
        }
        
        return $combinations;
    }
    
    /**
     * Get group IDs by categories
     */
    private function getGroupIdsByCategories($categories, $userId)
    {
        $query = ContactGroup::active()->whereIn('category', $categories);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query->pluck('id')->toArray();
    }
    
    /**
     * Get relevance reason for group suggestion
     */
    private function getRelevanceReason($group, $content)
    {
        $keywords = $group->getKeywords();
        $matchedKeywords = [];
        
        foreach ($keywords as $keyword) {
            if (str_contains(strtolower($content), strtolower($keyword))) {
                $matchedKeywords[] = $keyword;
            }
        }
        
        if (empty($matchedKeywords)) {
            return 'General relevance';
        }
        
        return 'Matches: ' . implode(', ', array_slice($matchedKeywords, 0, 3));
    }
    
    /**
     * Get message template suggestions based on content
     */
    public function getTemplateSuggestions($content)
    {
        $content = strtolower($content);
        $suggestions = [];
        
        $templates = MessageTemplate::active()->get();
        
        foreach ($templates as $template) {
            $relevanceScore = $template->getRelevanceScore($content);
            if ($relevanceScore > 0) {
                $suggestions[] = [
                    'template' => $template,
                    'score' => $relevanceScore,
                    'matched_keywords' => $this->getMatchedKeywords($template, $content)
                ];
            }
        }
        
        // Sort by relevance score
        usort($suggestions, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_slice($suggestions, 0, 5); // Top 5 suggestions
    }
    
    /**
     * Get matched keywords for template
     */
    private function getMatchedKeywords($template, $content)
    {
        $matched = [];
        foreach ($template->keywords as $keyword) {
            if (str_contains($content, strtolower($keyword))) {
                $matched[] = $keyword;
            }
        }
        return $matched;
    }
}



