import { useEffect, useState } from 'react';
import { useRouter } from 'next/router';
import { surveyAPI } from '@/lib/api';
import { SurveyData, CompanyValue } from '@/types';
import clsx from 'clsx';

interface RatingState { [valueId: number]: number }

export default function SurveyPage() {
  const router = useRouter();
  const { token } = router.query;
  const [survey, setSurvey] = useState<SurveyData | null>(null);
  const [ratings, setRatings] = useState<RatingState>({});
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!token) return;
    surveyAPI.fetch(token as string)
      .then(({ data }) => setSurvey(data))
      .catch((err) => {
        const msg = err?.response?.data?.detail;
        setError(msg || 'Survey not found or no longer active.');
      })
      .finally(() => setLoading(false));
  }, [token]);

  const setRating = (valueId: number, score: number) => {
    setRatings((prev) => ({ ...prev, [valueId]: score }));
  };

  const ratedCount = Object.keys(ratings).length;
  const totalValues = survey?.values.length ?? 0;
  const progress = totalValues > 0 ? Math.round((ratedCount / totalValues) * 100) : 0;

  const handleSubmit = async () => {
    if (!survey || !token) return;
    if (ratedCount < totalValues) {
      setError(`Please rate all ${totalValues} values before submitting.`);
      return;
    }
    setError('');
    setSubmitting(true);
    try {
      const payload = Object.entries(ratings).map(([valueId, score]) => ({
        company_value: Number(valueId),
        score,
      }));
      await surveyAPI.submit(token as string, payload);
      setSubmitted(true);
    } catch (err: unknown) {
      const msg = (err as { response?: { data?: { detail?: string } } })?.response?.data?.detail;
      setError(msg || 'Submission failed. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="w-12 h-12 border-4 border-brand-500 border-t-transparent rounded-full animate-spin" />
      </div>
    );
  }

  if (error && !survey) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div className="text-center">
          <div className="text-5xl mb-4">⚠️</div>
          <h2 className="text-xl font-semibold text-gray-800">{error}</h2>
          <p className="text-gray-500 mt-2">Please contact your administrator for a new link.</p>
        </div>
      </div>
    );
  }

  if (submitted) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-brand-50 to-brand-100 flex items-center justify-center px-4">
        <div className="bg-white rounded-2xl shadow-lg p-10 max-w-md w-full text-center">
          <div className="text-6xl mb-4">🎉</div>
          <h2 className="text-2xl font-bold text-gray-900 mb-2">Thank you!</h2>
          <p className="text-gray-500">
            Your anonymous response has been submitted for <strong>{survey?.company_name}</strong>.
          </p>
          <p className="text-sm text-gray-400 mt-4">You may close this window.</p>
        </div>
      </div>
    );
  }

  if (!survey) return null;

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Sticky header */}
      <div className="sticky top-0 z-10 bg-white border-b border-gray-200 shadow-sm">
        <div className="max-w-2xl mx-auto px-4 py-4">
          <div className="flex items-center justify-between mb-3">
            <div className="flex items-center gap-3">
              {survey.company_logo && (
                <img src={survey.company_logo} alt="logo" className="h-8 object-contain" />
              )}
              <div>
                <h1 className="font-bold text-gray-900 text-sm sm:text-base">{survey.company_name}</h1>
                <p className="text-xs text-gray-400">{survey.assessment_title}</p>
              </div>
            </div>
            <span className="text-sm text-gray-500 font-medium">
              {ratedCount}/{totalValues} rated
            </span>
          </div>
          {/* Progress bar */}
          <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div
              className="h-full bg-brand-500 rounded-full transition-all duration-300"
              style={{ width: `${progress}%` }}
            />
          </div>
        </div>
      </div>

      <div className="max-w-2xl mx-auto px-4 py-8 space-y-5">
        <div className="text-center pb-2">
          <h2 className="text-xl font-bold text-gray-900">Rate How Well We Live Our Values</h2>
          <p className="text-gray-500 text-sm mt-1">
            1 = Not at all &mdash; 10 = Completely embedded. This is 100% anonymous.
          </p>
        </div>

        {survey.values.map((value: CompanyValue) => (
          <ValueCard
            key={value.id}
            value={value}
            selectedScore={ratings[value.id]}
            onSelect={(score) => setRating(value.id, score)}
          />
        ))}

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
            {error}
          </div>
        )}

        <button
          onClick={handleSubmit}
          disabled={submitting || ratedCount < totalValues}
          className={clsx(
            'w-full py-4 rounded-xl text-white font-bold text-lg transition-all',
            ratedCount === totalValues
              ? 'bg-brand-500 hover:bg-brand-600 shadow-lg hover:shadow-xl'
              : 'bg-gray-300 cursor-not-allowed',
            submitting && 'opacity-70'
          )}
        >
          {submitting ? 'Submitting…' : ratedCount < totalValues
            ? `Rate ${totalValues - ratedCount} more to submit`
            : 'Submit My Ratings'}
        </button>

        <p className="text-center text-xs text-gray-400">
          Your response is completely anonymous. No personal information is collected.
        </p>
      </div>
    </div>
  );
}

function ValueCard({
  value, selectedScore, onSelect,
}: {
  value: CompanyValue;
  selectedScore: number | undefined;
  onSelect: (score: number) => void;
}) {
  return (
    <div className={clsx(
      'bg-white rounded-2xl border shadow-sm p-5 transition-all',
      selectedScore ? 'border-brand-300 shadow-brand-100' : 'border-gray-100'
    )}>
      <div className="flex items-start justify-between mb-3">
        <div className="flex-1 min-w-0 pr-3">
          <h3 className="font-semibold text-gray-900">{value.name}</h3>
          {value.description && (
            <p className="text-sm text-gray-500 mt-0.5">{value.description}</p>
          )}
        </div>
        {selectedScore && (
          <span className="text-2xl font-bold text-brand-600 flex-shrink-0">
            {selectedScore}/10
          </span>
        )}
      </div>

      <div className="flex gap-1.5 flex-wrap">
        {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map((score) => (
          <button
            key={score}
            onClick={() => onSelect(score)}
            className={clsx(
              'w-9 h-9 rounded-lg text-sm font-semibold transition-all border-2',
              selectedScore === score
                ? 'bg-brand-500 text-white border-brand-500 scale-110 shadow-md'
                : selectedScore && score < selectedScore
                  ? 'bg-brand-100 text-brand-600 border-brand-200'
                  : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-brand-300 hover:bg-brand-50'
            )}
          >
            {score}
          </button>
        ))}
      </div>
    </div>
  );
}
